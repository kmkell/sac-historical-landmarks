"""
Script Name: parse_historic_register.py
Description: Parses the official City of Sacramento Register of Historical 
             and Cultural Resources (2020 Complete Register dataset sourced 
             from the Preservation Sacramento / City of Sacramento website), 
             cleans text encoding issues, standardizes dates, deduplicates 
             entries, and exports a clean CSV for database migration.
"""

import csv
import re
from datetime import datetime

# Open the text file with cp1252 encoding (update path if your file is located elsewhere)
with open(r'C:\wamp64\www\sac-historical-landmarks\database\migrations\complete_register.txt', 'r', encoding='cp1252') as f:
    text = f.read()

def clean_text(value):
    if not value:
        return ''
    value = value.replace('â€™', "'")\
                 .replace('â€œ', '"')\
                 .replace('â€', '"')\
                 .replace('\xa0', ' ')
    return value.strip()

def parse_date(date_str):
    if not date_str:
        return ''
    date_str = clean_text(date_str)
    
    formats_to_try = [
        '%b %d, %Y',  # e.g., Apr 27, 1982
        '%d-%b-%y',  # e.g., 15-Jun-82
        '%B %d, %Y',  # e.g., April 27, 1982
        '%b. %d, %Y', # e.g., Feb. 15, 2011
    ]
    
    cleaned_date = date_str.replace('.', '')
    
    for fmt in formats_to_try:
        try:
            dt = datetime.strptime(cleaned_date, fmt)
            if dt.year > 2050:
                dt = dt.replace(year=dt.year - 100)
            return dt.strftime('%Y-%m-%d')
        except ValueError:
            continue
            
    return date_str

# Stricter pattern ensuring addresses must end with a valid street type and have a clean newline break
pattern = re.compile(
    r'(?P<address>^\d+(?:[-\/\d]+)?\s+[A-Za-z0-9\s#&-]+(?:Street|St|Avenue|Ave|Boulevard|Blvd|Drive|Dr|Way|Court|Ct|Place|Pl|Lane|Ln|Road|Rd))\s*\n+'
    r'APN:\s*(?P<apn>[\d,-]+)\s*'
    r'Ord\. No\.\s*(?P<ord_no>[\w.-]+)\s*'
    r'Enacted:\s*(?P<enacted>[^\n]+?)\s*'
    r'Construction Date:\s*(?P<construction>[^\n]+)'
    r'(?:\s*Historic Name:\s*(?P<historic_name>[^\n]+))?',
    re.IGNORECASE | re.MULTILINE
)

matches = pattern.finditer(text)

seen_records = set()
rows = []

for match in matches:
    data = match.groupdict()
    
    address = clean_text(data['address'])
    apn = clean_text(data['apn'])
    ord_no = clean_text(data['ord_no'])
    enacted = parse_date(data['enacted'])
    construction = clean_text(data['construction'])
    historic_name = clean_text(data['historic_name'])
    
    # Unique key signature to prevent duplicate passes from the PDF text layer
    unique_key = (address.lower(), apn, ord_no)
    
    if unique_key not in seen_records:
        seen_records.add(unique_key)
        rows.append({
            'address': address,
            'apn': apn,
            'ord_no': ord_no,
            'enacted': enacted,
            'construction_date': construction,
            'historic_name': historic_name
        })

csv_file = 'parsed_historic_register.csv'
fields = ['address', 'apn', 'ord_no', 'enacted', 'construction_date', 'historic_name']

with open(csv_file, 'w', newline='', encoding='utf-8-sig') as f:
    writer = csv.DictWriter(f, fieldnames=fields)
    writer.writeheader()
    writer.writerows(rows)

print(f"Successfully cleaned, deduplicated, and extracted {len(rows)} unique records into {csv_file}!")
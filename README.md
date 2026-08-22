# Sacramento Historic Landmarks ETL Pipeline & Explorer

An automated data pipeline and application designed to ingest, clean, normalize, and serve public historical landmark data from the City of Sacramento. 

## 🚀 The Challenge & Discovery
While building the initial ETL pipeline using a raw CSV export of the city's dataset, I ran into a major data integrity hurdle: standard spreadsheet handling was corrupting long 14-digit Assessor's Parcel Numbers (APNs) into exponential notation (e.g., `2.25E+13`), truncating critical identifiers. 

Upon inspecting the city's original source file, I noticed that the `.xlsx` format preserved the data correctly without scientific notation. This required pivoting away from a direct CSV import to a hybrid multi-language approach.


## 🛠️ Architecture & Data Flow

### Phase 1: Core Master Dataset ETL Pipeline
To ingest and normalize the official City of Sacramento master spreadsheet without data corruption:
1. **Python Excel Bridge (`convert_excel.py`):** Automatically reads the raw `historic_landmarks_raw.xlsx` file using **Pandas**, forcing numeric fields to be treated strictly as strings to prevent long 14-digit APNs from collapsing into scientific notation.
2. **PHP Staging & Ingestion (`import_landmarks.php`):** Wipes temporary staging tables, opens clean CSV data streams, and handles database loads via PDO with built-in UTF-8 BOM character stripping.
3. **Database Normalization & Transformation (MySQL & PHP):** Executes a master SQL migration script that:
   * **APN Standardization:** Uses Regular Expressions (`REGEXP`) to isolate valid 14-digit APNs and format them into readable dashed strings (`XXX-XXXX-XXX`), while gracefully handling empty fields (`'UNKNOWN'`).
   * **Address Synthesis:** Combines fragmented raw fields (`house`, `street_name`, `street_type`) into a unified `street_address` column using `CONCAT_WS`.
   * **Intelligent Fallbacks:** Dynamically generates fallback resource titles for properties lacking official landmark names.
   * **Whitespace Sanitization:** Strips invisible UTF-8 BOM characters in PHP and applies rigorous `TRIM()` logic across all text fields.
   * **Audit Logging:** Automatically records pipeline execution counts and status to an `etl_log` table upon completion.

### Phase 2: Archival Data Enrichment Pipeline
To cross-reference and enrich the master dataset with historical context:
1. **Historical Register Text Parser (`parse_historic_register.py`):** 
   * Ingests raw text/PDF dumps from the official Sacramento Register of Historical and Cultural Resources.
   * Cleans text encoding issues (`cp1252` mojibake) and runs a multi-format date parsing fallback engine (`datetime.strptime`).
   * Uses named regular expression capture groups to extract clean structural fields (construction dates, ordinance numbers, historical 
   names) and filters duplicates using composite unique key tuples.

## 🖥️ Front-End Application & User Experience
Beyond the backend ETL pipeline, the application features a clean, responsive web interface built for both local history hobbyists and data explorers:

* **Search & Listing Interface (`index.php`):** Provides a searchable, user-friendly view of all city landmarks, allowing visitors to instantly filter by address, name, or attributes.
* **Comprehensive Detail View (`detail.php`):** Pulls together core municipal data, historical enrichment data, and custom research notes into a single unified profile page.
* **Research & Curation Workflow (`edit_research.php`, `update_research.php`):** Admin-facing data management tools designed to capture ongoing historical research and notes (secured for production deployment).

## ⚙️ Tech Stack
* **Back-End & Automation:** PHP (PDO, Server-Side Rendering), Python (Pandas, Openpyxl)
* **Front-End:** HTML5, CSS3 (Custom Responsive Styling via `styles.css`), Modern PHP Templating
* **Database & Architecture:** MySQL, Relational Database Normalization, Staging-to-Production ETL Architecture
* **Version Control:** Git & GitHub

## 📂 Project Structure
```text
├── assets/
│   └── styles.css                    # Front-end styling rules
├── database/
│   ├── backups/                      # Local database snapshot storage
│   ├── migrations/
│   │   ├── fix_apn_data.sql          # APN cleanup migration scripts
│   │   ├── migrate_landmarks.sql     # Production migration routines
│   │   ├── parse_historic_register.py# Archival text/PDF parsing script
│   │   └── schema_modifications.sql  # Database schema updates
│   ├── schema.sql/
│   │   └── sac_historic_homes.sql    # Base database schema definitions
│   └── seeds/
│       ├── Complete_Register.txt     # Raw historical register source text
│       └── parsed_historic_register.csv # Output enriched dataset
├── convert_excel.py                  # Python bridge for Excel-to-CSV conversion
├── dbconnect.php                     # Secure database connection configuration
├── detail.php                        # Individual landmark detail view
├── header.php                        # Reusable layout header component
├── import_landmarks.php              # Main PHP ETL automation controller
├── index.php                         # Front-end search and listing interface
├── LandmarkService.php               # Data service layer and business logic
├── historic_landmarks_raw.xlsx       # Raw master dataset from the city
├── sac_landmarks_raw.csv             # Intermediary clean CSV data stream
├── .gitignore                        # Excludes bulky data files and local configs
└── README.md                         # Project documentation
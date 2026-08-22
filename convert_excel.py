import pandas as pd

# Updated to match the actual filename from the city
df = pd.read_excel('historic_landmarks_raw.xlsx', dtype=str)

df.to_csv('sac_landmarks_raw.csv', index=False)

print("Excel converted to clean CSV successfully!")
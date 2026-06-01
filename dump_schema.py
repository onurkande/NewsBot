import sqlite3
import os

db_path = os.path.join(r"C:\Users\Kande\Documents\GitHub\NewsBot\services\twscrape", "accounts.db")
try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    cursor.execute("SELECT name, sql FROM sqlite_master WHERE type='table';")
    tables = cursor.fetchall()
    for table_name, schema in tables:
        print(f"Table: {table_name}")
        print(f"Schema:\n{schema}\n")
    conn.close()
except Exception as e:
    print(f"Error: {e}")

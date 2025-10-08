import yaml
import json

# Read the YAML file
with open('docs/openapi.yaml', 'r', encoding='utf-8') as yaml_file:
    data = yaml.safe_load(yaml_file)

# Write to JSON file
with open('storage/api-docs/api-docs.json', 'w', encoding='utf-8') as json_file:
    json.dump(data, json_file, indent=2)

print("Successfully converted openapi.yaml to api-docs.json")
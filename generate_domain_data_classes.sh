#!/bin/bash

# Set the path to the app directory
APP_PATH="app"

# Set the path to the Models directory
MODELS_PATH="$APP_PATH/Models"

# Set the path to the DomainData directory
DOMAIN_DATA_PATH="$APP_PATH/DomainData"

# Create the Domain Data folder if it doesn't exist
mkdir -p "$DOMAIN_DATA_PATH"

# Loop through each PHP file in the Models directory
for f in "$MODELS_PATH"/*.php; do
    # Extract the file name without extension (i.e., the class name)
    FILENAME=$(basename "$f" .php)

    # Define the model class name
    MODEL_CLASS="$FILENAME"

    # Define the domain data trait name based on the model class name
    DOMAIN_DATA_TRAIT="$MODEL_CLASS""Dto"

    # Define the domain data file path
    DOMAIN_DATA_FILE="$DOMAIN_DATA_PATH/$DOMAIN_DATA_TRAIT.php"

    # Check if the domain data file already exists
    if [ ! -f "$DOMAIN_DATA_FILE" ]; then
        # Create the domain data file
        touch "$DOMAIN_DATA_FILE"

        # Write the domain data trait structure to the file
        cat <<EOF > "$DOMAIN_DATA_FILE"
<?php

namespace App\DomainData;

trait $DOMAIN_DATA_TRAIT
{
    public function getRules(array \$fields = []): array
    {
        \$data = \$this->initialize$DOMAIN_DATA_TRAIT();
        
        if (sizeof(\$fields) == 0)
            return \$data;
            
        return array_intersect_key(\$data, array_flip(\$fields));
    }
    
    public function initialize$DOMAIN_DATA_TRAIT(): array
    {
        \$data = [
            'name' => ['required', 'string', 'max:60'],
        ];
        
        if (isset(\$this->fillable) && sizeof(\$this->fillable) == 0) {
            \$this->fillable = array_keys(\$data);
        }
        
        return \$data;
    }
}
EOF

        echo "Domain data trait '$DOMAIN_DATA_TRAIT' created for model '$MODEL_CLASS'"
    else
        echo "Domain data trait '$DOMAIN_DATA_TRAIT' already exists for model '$MODEL_CLASS'"
    fi
    sleep 1
done

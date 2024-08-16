#!/bin/bash

# Set the path to the app directory
APP_PATH="app"

# Set the path to the Models directory
MODELS_PATH="$APP_PATH/Models"

# Set the path to the CONTROLLERS directory
CONTROLLERS_PATH="$APP_PATH/Http/Controllers"

# Create the CONTROLLERS folder if it doesn't exist
mkdir -p "$CONTROLLERS_PATH"

# Loop through each PHP file in the Models directory
for f in "$MODELS_PATH"/*.php; do
    # Extract the file name without extension (i.e., the class name)
    FILENAME=$(basename "$f" .php)

    # Define the model class name
    MODEL_CLASS="$FILENAME"

    # Get the first character
    firstChar="${MODEL_CLASS:0:1}"
    # Convert the first character to lowercase
    firstCharLower=$(tr '[:upper:]' '[:lower:]' <<< "$firstChar")

    # Define the VARIABLE_SERVICE based on the model class name
    VARIABLE_SERVICE="${firstCharLower}${MODEL_CLASS:1}"

    # Define the CONTROLLER class name based on the model class name
    CONTROLLER_CLASS="${MODEL_CLASS}Controller"

    # Define the CONTROLLER file path
    CONTROLLER_FILE="$CONTROLLERS_PATH/$CONTROLLER_CLASS.php"

    # Check if the CONTROLLER file already exists
    if [ ! -f "$CONTROLLER_FILE" ]; then
        # Create the CONTROLLER file
        touch "$CONTROLLER_FILE"

        # Write the CONTROLLER class structure to the file
        cat <<EOF > "$CONTROLLER_FILE"
<?php

namespace App\Http\Controllers;

use App\DomainData\\${MODEL_CLASS}Dto;
use App\Services\\${MODEL_CLASS}Service;

class $CONTROLLER_CLASS extends Controller
{
    use ${MODEL_CLASS}Dto;

    public function __construct(private ${MODEL_CLASS}Service \$${VARIABLE_SERVICE}Service)
    {
    }
}
EOF

        echo "CONTROLLER class '$CONTROLLER_CLASS' created for model '$MODEL_CLASS'"
    else
        echo "CONTROLLER class '$CONTROLLER_CLASS' already exists for model '$MODEL_CLASS'"
    fi
    sleep 1
done

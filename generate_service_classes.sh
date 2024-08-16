#!/bin/bash

# Set the path to the app directory
APP_PATH="app"

# Set the path to the Models directory
MODELS_PATH="$APP_PATH/Models"

# Set the path to the Services directory
SERVICES_PATH="$APP_PATH/Services"

# Create the Services folder if it doesn't exist
mkdir -p "$SERVICES_PATH"

# Loop through each PHP file in the Models directory
for f in "$MODELS_PATH"/*.php; do
    # Extract the file name without extension (i.e., the class name)
    FILENAME=$(basename "$f" .php)

    # Define the model class name
    MODEL_CLASS="$FILENAME"

    # Define the service class name based on the model class name
    SERVICE_CLASS="$MODEL_CLASS""Service"

    # Define the service file path
    SERVICE_FILE="$SERVICES_PATH/$SERVICE_CLASS.php"

    # Check if the service file already exists
    if [ ! -f "$SERVICE_FILE" ]; then
        # Create the service file
        touch "$SERVICE_FILE"

        # Write the service class structure to the file
        cat <<EOF > "$SERVICE_FILE"
<?php

namespace App\Services;

class $SERVICE_CLASS extends CrudService
{
    public function __construct()
    {
        parent::__construct("$MODEL_CLASS");
    }

    // Implement your service methods here
}
EOF

        echo "Service class '$SERVICE_CLASS' created for model '$MODEL_CLASS'"
    else
        echo "Service class '$SERVICE_CLASS' already exists for model '$MODEL_CLASS'"
    fi
    sleep 1
done

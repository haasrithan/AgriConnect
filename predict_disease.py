import sys
import tensorflow as tf
from tensorflow.keras.preprocessing import image
import numpy as np
from PIL import Image

# Load the trained model
model = tf.keras.models.load_model('model.h5')

# Get the image path from the command line argument
image_path = sys.argv[1]

# Preprocess the uploaded image
img = Image.open(image_path)
img = img.resize((224, 224))  # Resize to match the model input size
img_array = np.array(img) / 255.0  # Normalize the image
img_array = np.expand_dims(img_array, axis=0)  # Add batch dimension

# Predict the disease
prediction = model.predict(img_array)

# Map the prediction to a label (example, adjust based on your model)
labels = ["Healthy", "Disease1", "Disease2"]  # Adjust as per your model's output
predicted_label = labels[np.argmax(prediction)]

# Output the prediction
print(predicted_label)

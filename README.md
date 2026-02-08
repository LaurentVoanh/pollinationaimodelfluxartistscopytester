
# ArtBase: Artist Benchmark Engine (Flux Model)

## Overview
ArtBase is a specialized research tool designed to benchmark the stylistic capabilities of the **Flux model** (via Pollinations AI). This script allows you to systematically test at least **2,400 famous artists** to observe how accurately the AI reproduces their unique aesthetic.

By defining a fixed theme (e.g., "a futuristic city" or "a portrait of a cat"), you can analyze the model's performance artist by artist. This facilitates **deep learning analysis** and helps creators understand the model's strengths and limitations regarding specific artistic influences.

---

## How It Works

The engine operates on a "One-Artist-Per-Image" logic:
1. **User Input:** You enter a subject/theme in the top form.
2. **Sequential Iteration:** The script reads your `artiste.txt` list line by line.
3. **Prompt Engineering:** It generates a specific prompt for each artist: `create in the style of artist [Name] this: [Subject]`.
4. **Automated Comparison:** You can visually compare how different artists "interpret" the same prompt, allowing for a pure benchmark without style-blending.

---

## Installation & Setup

### 1. API Configuration
To make the script functional, you must provide your API key.
* Open `index1.php`.
* Locate **Line 47** (approx.).
* Replace the placeholder with your actual key:
  ```php
  $apiKey = 'YOUR_POLLINATIONS_API_KEY';

```

### 2. Directory Permissions (Image Storage)

The script is designed to save images and metadata in a folder named `imge/`.

* The script will attempt to create this folder automatically with `chmod 0777`.
* **Manual Setup:** If the folder is not created due to server restrictions, create a directory named `imge` in the root folder and ensure it has write permissions:
```bash
mkdir imge
chmod 777 imge

```



### 3. Artist Database

Ensure you have a file named `artiste.txt` in the root directory. Each line should contain the name of one artist.

---

## Advantages

* **Scientific Benchmarking:** Evaluate AI accuracy for over 2,400+ famous styles.
* **Metadata Tracking:** Each generation saves a `.json` file containing the seed, artist name, and full prompt for future reference.
* **No Style Dilution:** By isolating artists, you discover exactly which ones the Flux model masters best.
* **Deep Learning Insight:** Gain a professional understanding of model capabilities on themes you define yourself.

---

## Security Note

This project includes a simple password protection layer. The default password is set in the `$password` variable within the PHP script, for now the password is : laurent. Change it before deploying to a public server.

```

### Why this is good for GitHub:
* **Clear Value Proposition:** It explains *why* someone should use it (Deep Learning / Benchmarking).
* **Technical Clarity:** It points exactly to the lines of code that need modification.
* **Professional Tone:** It uses terms like "Sequential Iteration" and "Style Dilution" which appeal to the AI community.

Would you like me to help you create a **license file** (like MIT) to go along with this on GitHub?

```

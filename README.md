# TYPO3 Extension "placeholder_images"
Easily add placeholder images in TYPO3 using either external sources (like placeholder.com) or generating them on the fly.

## What does it do?

This extensions adds a button "Add placeholder image" to content elements and other data records using FAL media fields in the TYPO3 Backend. 
This makes it easy to quickly add a few generated placeholder images for prototyping. Support uploading multiple placeholders images at once.

![Add placeholder image Button](/Resources/Public/Screenshots/placeholder_button.png)


![Placeholder modal](/Resources/Public/Screenshots/placeholder_modal.png)

## Requirements

Currently support TYPO3 8.7 and 9.5 LTS

## 1. Installation

### Installation with composer

`composer require christianessl/placeholder_images`. 

Clear caches and reload the backend for all changes to take effect.

### Installation with TER

Open the TYPO3 Extension Manager, search for `placeholder_images` and install the extension.

Clear caches and reload the backend for all changes to take effect.

## 2. Configuration

Initially after installing the extension, the new button will only show up if TYPO3_CONTEXT is set to **Development**, which can be adjusted in the Extension Configuration.

### Configuration in TYPO3 8.7

- Go to Admin Tools -> Extensions -> Extension *placeholder_images*
- Click the extension name to access the Extension Configuration.

### Configuration in TYPO3 9.5

- Go to Admin Tools -> Settings -> Extension Configuration
- Click *Configure extensions* and the *placeholder_images* to access the configuration.

### Configuration options ###

- **typo3Contexts** *(default: Development)*
    - Comma separated list of TYPO3_CONTEXT settings for which the placeholder button should be displayed.
- **defaultWidth** 
    - Default width of the generated images (changeable in the modal)
- **defaultHeight** 
    - Default height of the generated images (changeable in the modal)
- **defaultFormat** *(default: png)*
    - Default file format of the generated images (changeable in the modal)
- **defaultText** 
    - Default placeholder text (changeable in the modal). Shown as "1024x768", if empty.
- **defaultBGColor** 
    - Default background color of the generated images (changeable in the modal)
- **defaultTextColor** 
    - Default text color of the generated images (changeable in the modal)
- **imageSource** 
    - The source from which the images should be generated. There are 3 options at the moment:
        - placeholder.com (external)
        - imagemagick (local image generation)
        - custom (connect your own image generation service)
- **customSourceUrl** 
    - If you want to connect your own image generation service, place the url here in a format like:
        - //source-example.local/?width={width}&height={height}&bgcolor={bgcolor}&textcolor={textcolor}&format={format}&text={text}

## 3. Usage

Create a new content element like *Text / Media* and enjoy adding a few placeholder images.      

![Placeholder frontend](/Resources/Public/Screenshots/placeholder_frontend.png)

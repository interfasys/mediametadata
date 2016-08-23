# Media Metadata

## Description

A cloud application, which provides CRUD access to the metadata stored in media files. 

Images can contain extra information embedded in their header. The goal of this app is to extract that information at upload time and to store it in the database so that it can be used later.

The three main types of metadata extracted in this application are as follows:

- EXIF Metadata
- IPTC Metadata [Not Implemented Yet]
- XMP Metadata [Not Implemented Yet]

Here is the list of fields that we are currently or intend on extracting:

- Date and Time
- GPS Location and Coordinates
- Image Width and Image Height
- Image Orientation
- Camera Make
- Camera Model
- Image Description
- Creator
- User Comments

## Maintainers

- [Jalpreet Singh Nanda](https://github.com/imjalpreet)
- [Olivier Paroz](https://github.com/oparoz)

This project was developed during the Google Summer of Code 2016

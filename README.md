# Document System Backend

## Overview

The focus of this project was on creating the middleware for synchronizing files of the document system with an external server. Though, it also has two core features for a document system: a page for uploading documents from the local computer and another one for searching documents based on some categories (aka. name, owner of document, creation date, and document type). These pages along with the other scripts were created using HTML and PHP, but due to time constraints, no styling was added to them. 

## Set Up

This document system is hosted in a virtual machine (VM) with Ubuntu Server as its operating system, and the VM is created using Oracle VirtualBox. To display the pages of the document system, the server uses Nginx as the web service, and to store the data, it uses MySQL as the database service. It also includes phpMyAdmin to manage the database using an interface.

## Middleware

The purpose of the middleware is to synchronize the files of the document system with an external server. To do this, it uses cron jobs to run two scripts every hour. The first script requests the files not requested before from the external server and saves them in a temporary folder in the server of the document system. Then, the second script processes the files to put them in the same format as the files in the system, and once processed, the script saves those files in the directory and database of the document system. 

 

# Conference
Component to manage conferences


## Building the installation package
To build the installation package robo.li is required. The installation of robo.li can be done as:

```composer global require consolidation/robo```

Once robo.li has been installed take the following steps:
1. Go to the folder **build**
2. Run the command ```robo build:package```
3. The release package is created in the folder **releases**

### Change of version number
The version number can be set by editing the RoboFile.php file and change the value of the semVer variable.
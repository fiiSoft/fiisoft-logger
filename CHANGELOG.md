# Changelog

All important changes to `fiisoft-logger` will be documented in this file

## 2.0.0

New methods (setLevels and getLevels) was added to interface LogsMonitor.

Command ShowQueueLogsCommand can now handle option --show-levels and display available levels of logs.

## 1.4.0

Updated dependencies. Method ShowQueueLogsCommand::handleInput() returns status code now (instead of exit). 

## 1.3.0

Added class ConsoleLogger. It echos log messages directly to the STDOUT.

## 1.2.0

Added two classes and three simple tests.
Fixed bug with not-working properly LogsMonitor::filterByLevel() for non-default log levels.

## 1.1.0

 - added InstantSmartLogger and InstantLogsMonitor.
 - added AbstractLogsMonitor
 - DefaultLogsMonitor inherits from AbstractLogsMonitor 
 - DefaultSmartLogger is not final

## 1.0.0

Initial version of library.
# Changelog

All important changes to `fiisoft-logger` will be documented in this file

## 3.1.0

NumberOfWritesPerTimeConstraint optimised for number of write equal 1 and 0.

## 3.0.0

There are no new features in this release. 
It is just backward-incompatible because all classes have been moved to other namespaces.

## 2.2.0

Added new writers:
* GenericLogsWriter
* LimitedLogsWriter
* MultiLogsWriter

Also added three implementations of Constraint to use for LimitedLogsWriter:
* GenericConstraint
* MaxNumberOfWritesConstraint
* NumberOfWritesPerTimeConstraint

## 2.1.0

Added new classes:
* adapter of SmartLogger to Symfony's OutputInterface: SymfonyOutputLoggerAdapter 
* two new writers: BufferedWriter and ConsoleWriter

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
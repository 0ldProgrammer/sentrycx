const {app} = require('electron')
const log = require('electron-log');
const path = require('path')



exports.logINFO = function(logMessage)
{
    log.transports.file.resolvePath = () => path.join(app.getAppPath(), 'logs/main.log');
    log.info(logMessage)
}

exports.logError = function(errorMessage)
{
    log.transports.file.resolvePath = () => path.join(app.getAppPath(), 'logs/main.log');
    log.error(errorMessage);
}
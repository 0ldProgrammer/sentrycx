// main.js

// Modules to control application life and create native browser window
const { app, BrowserWindow, Tray,Menu } = require('electron')

app.commandLine.appendSwitch('ignore-certificate-errors')
const path = require('path');
const log = require('./src/modules/logger');
const AD = require('./src/modules/ad')
let tray = null

log.logINFO('LapsCX is Initializing');

  app.whenReady().then(() => {
    tray = new Tray(path.join(__dirname,'admin_laps_icon.png'))
    const contextMenu = Menu.buildFromTemplate([
      { label: 'LapsCX v:1.0.1' },
      { label: '', type: 'separator' },
      { label: 'Quit', click() { app.quit(); } }
    ])
    tray.setToolTip('LapsCX is Running')
    tray.setContextMenu(contextMenu)
    
    log.logINFO('LapsCX is Runing');
    
    const fetch = require('electron-fetch').default
    const window = BrowserWindow.getFocusedWindow();
    
    log.logINFO('Fetching Unlisted Records');
    fetch('http://localhost:8000/records/unlisted')
        .then(res => res.text())
        .then(body => {
            this.details = JSON.parse(body).list;

              AD.authenticate(this.details);
              // AD.getDetails(this.details)

        }
            
        ).catch(err => {
          log.logINFO("An Error has Occurred, Please check error log for more info")
          log.logError(err)
        });       
  });

  
// Quit when all windows are closed, except on macOS. There, it's common
// for applications and their menu bar to stay active until the user quits
// explicitly with Cmd + Q.
app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit()
})

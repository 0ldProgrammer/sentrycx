
require('dotenv').config();
const { app } = require('electron')
const log = require('./logger');
const fetch = require('electron-fetch').default

var ActiveDirectory = require('activedirectory2').promiseWrapper;

const sleep = ms => new Promise(resolve => setTimeout(closeApp, ms));

function getAD(dc = 'concentrix')
{
  var config = {
            url: `ldap://${process.env.cnx_ldap_url}`,
            baseDN: `dc=${dc},dc=com`,
            username: process.env.AD_username,
            password: process.env.AD_password,
            bindDN : process.env.AD_username,
            bindCredentials : process.env.AD_password
          }
  var ad = new ActiveDirectory(config);
  return ad;
}


exports.authenticate = function(unlisted)
{
    var ad = getAD();
    var username = process.env.AD_username;
    var password = process.env.AD_password;
    log.logINFO('Authenticating AD...')
    ad.authenticate(username, password, function(err, auth) {
        if (err) {
          console.log('ERROR: '+JSON.stringify(err));
          sleep(5000);
          return false;
        }
        if (auth) {
            log.logINFO('AD Authenticated!');
            processDetails(unlisted)

          return true;
        }
        else {
          log.logError('Authentication Failed!');
          return false;
        }
    });
   
}

const processDetails = async (details) => {
    for (const users of Object.values(details)) {
        console.log('');
        console.log(users.username)
        log.logINFO('Attempting to fetch '+users.username+' from AD');
        const result = await fetchADData(users.username);
        console.log(result);
    }
    log.logINFO('Data Processing Done. Closing Application...')
     
    sleep(10000);
}
  
const fetchADData = username => {
    var ad = getAD();
    return new Promise((resolve, reject) => {
        ad.findUser(username, function(err, user) {
            if (err) {
                console.log('ERROR: ' +JSON.stringify(err));
                log.logError(JSON.stringify(err))
                resolve(JSON.stringify(err)) ;
            }
            
            if (! user) {
                log.logError('User: ' + username + ' not found.');
                resolve(username + ' not found.')
            }
            else{
                resolve(postResponse(JSON.stringify(user), username));
                
            }
        });
    });
}


function postResponse($data, username)
{
    return new Promise((resolve, reject) => {
        log.logINFO(`Attemping to Send ${username} Data to API `)
        fetch('http://localhost:8000/records/postResponse', { 
            method: 'POST', 
            body: $data,
            headers: {'Content-Type': 'application/json'} 
        })
            .then(res => res.json())
            .then(json => {
                if(json.status)
                {
                    log.logINFO('Data successfully Sent!')
                    log.logINFO(json.response)
                    resolve(json)
                }else{
                    log.logError(json.response)
                    resolve(json.response)

                }
            })
            .catch(err => {
                log.logError(err)
                log.logError(json.response)
                resolve(err)
            })
    })
    
}


function closeApp()
{
  log.logINFO('App closed');
  app.quit();
}

var fs = require('fs');
var path = require('path');
var contract = require('./contract.js')

const PORT = process.env.PORT || 3000;

var express = require('express');
var app = express();
var bodyParser = require('body-parser');

var gReponse = null;


var ServerStatusEnum = {
  OFFLINE: 1,
  ONLINE: 2
}

var ServerStatus = ServerStatusEnum.OFFLINE;

//Blockchain app
//Variables
//APP
const App = {
  account: null,
  aepp: null,
  
  start: async function() {
    try {
      ServerStatus = ServerStatusEnum.ONLINE;
    } catch (error) {
      console.error("Could not connect to contract or chain." + error);
    }
  },
  getGameBalance: async function(gameAddress, userAddress){
    console.log('app.getBal');
    return new Promise((resolve) => {
      contract.getGameBalance(gameAddress, userAddress).then((data) => {
        console.log(data);
        resolve(data);
      })
    });
  },
  killServer: function (){
    console.log("Server process killed");
    process.exit(1);
  },
  getServerStatus: async function(){
    gReponse = ServerStatus;
  }
};

//Initialize server
DIST_DIR = 'src';
app.use(express.static('src'));
app.use(bodyParser.json({limit:'50mb'}));
app.use(bodyParser.urlencoded({ limit:'50mb', extended: true }));

app.use((req, res, next) => {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Headers', 'Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Allow-Request-Method');
  res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.header('Allow', 'GET, POST, OPTIONS,');
  next();
});

var server = app.listen(PORT, function () {
  var host = server.address().address;
  var port = server.address().port;
  console.log("http://%s:%s", host, port);
 
  //Read config file
  /*console.log("Reading config file...");
  nodes = JSON.parse(fs.readFileSync(path.join(__dirname,'nodes_config.json'), 'utf-8'));
  console.log("Config file read!");
  selectedNode = nodes.nodes[0];
  */
  //Initialize Blockchain
  App.start();
});

//ROUTES
//----POST----

//-----GET-----
app.get('/serverStatus', function(req,res) {
  App.getServerStatus().then(() => {
    res.json({data: gReponse});
  });
});
app.get('/gameBalance', function(req, res) {
  var gameAddress = req.query.gameAddress;
  var userAddress = req.query.userAddress;
console.log('entra');
  App.getGameBalance(gameAddress, userAddress).then((response)=>{
    res.json({data: response.decodedResult});
  });
});

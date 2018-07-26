var express      = require('express');
var accesslog    = require('access-log');
var path         = require('path');
var favicon      = require('serve-favicon');
var cookieParser = require('cookie-parser');
var bodyParser   = require('body-parser');
var session      = require('express-session');
var multer       = require('multer');

var index          = require('./routes/index');
var employee       = require('./routes/employee');
var student        = require('./routes/student');
var api            = require('./routes/api');

//var ios = require('socket.io-express-session');

var app = express();
//process.env.NODE_ENV= "development";

// view engine setup
app.use('/views', express.static(__dirname + '/views'));

app.engine('html', require('ejs').renderFile);
app.set('view engine', 'ejs');

// uncomment after placing your favicon in /public
app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json({limit: '150mb'}));
app.use(bodyParser.urlencoded({ limit: '150mb', extended: true, parameterLimit:50000 }));
app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "https://172.16.0.212");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads/studentexams/videos', express.static(path.join(__dirname, 'uploads/studentexams/videos')));

app.use(session({secret: '*******', 
                 resave: true,
                 saveUninitialized: true}));

app.use('/', index);
app.use('/employee', employee);
app.use('/student', student);
app.use('/api', api);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  console.error(err.stack);
  res.status(err.status || 500);
  res.send('NOT FOUND.');
  //res.render('error');
});

module.exports = app;

/*var http   = require('http'),
    server = http.createServer(app).listen(80, function(){
        console.log('listening to port: 80\n');        
    });
*/


var fs = require('fs');
var http = require('http');
var https = require('https');

var httpsOptions = {
    key: fs.readFileSync('./secure/sct.key'),
    cert: fs.readFileSync('./secure/sct.crt')
};

var httpsServer = https.createServer(httpsOptions, app, function (req, res) {
    console.log('listening to port: 443\n'); 
}).on('error', function(err){
    console.log("https:Caught flash policy server socket error: ");
    console.log(err.stack);
}).listen(443);

http.createServer(function (req, res) {
    console.log('listening to port: 80\n'); 
    res.writeHead(301, { "Location": "https://" + req.headers['host'] + req.url });
    res.end();
}).on('error', function(err){
    console.log("http:Caught flash policy server socket error: ");
    console.log(err.stack);
}).listen(80);


var io = require('socket.io')(httpsServer);

var student_vs_socket = {}; // Using to map the student id with socket id.

io.on("connection", function (socket) {
    
    var cookie = socket.request.headers.cookie;
    var sess_id = cookie.split('; ')[0];
    sess_id     = sess_id.split('=')[1];
    student_vs_socket[sess_id] = socket.id;
    
    socket.on('test', function(data){
        //console.log(data);
    });

    socket.on("disconnect", function () {
        console.log('DisConnected');
    });
    
    socket.on("error", function (err) {
        console.log(err.stack);
    });
});

io.on('error', function(err) {
    console.log("io:Caught flash policy server socket error: ");
    console.log(err);
});

    
/* Default stuff ends here */

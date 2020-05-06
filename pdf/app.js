const fs = require('fs');
const pdf = require('pdf-parse');

let args = process.argv.slice(2);

let dataBuffer = fs.readFileSync(args[0]);

pdf(dataBuffer).then(function(data) {
    let stream = fs.createWriteStream(args[1] + '.txt');
    stream.once('open', function(fd) {
        stream.write(data.text);
        stream.end();
    });
});

const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /document\.getElementById\('breakdownBase'\)\.innerText = isJoinerAllowed \?\s*'Joiner \/ Open Group' :\s*'Private \/ Exclusive Group';/;

const newJs = `let openSlotsStr = '';
      if (isJoinerAllowed) {
          const vId = document.getElementById('vehicleSelect').value;
          const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 0;
          if (vCap > 0) {
              const remaining = vCap - headsCount;
              openSlotsStr = remaining > 0 ? \` (\$\{remaining\} slots available)\` : \` (Full)\`;
          }
      }

      document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
            'Joiner / Open Group' + openSlotsStr :
            'Private / Exclusive Group';`;

if (regex.test(text)) {
    text = text.replace(regex, newJs);
    fs.writeFileSync(path, text, 'utf8');
    console.log('done fixing tour type 3');
} else {
    console.log('regex not found');
}

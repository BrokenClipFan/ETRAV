const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex1 = /const depositContainer = document\.getElementById\('depositContainer'\);\s*if \(isCustomRoute\) \{/m;
const newCode1 = `const depositContainer = document.getElementById('depositContainer');

      // Calculate per person
      let perPersonPrice = headsCount > 0 ? (totalToPay / headsCount) : 0;
      if (isJoinerAllowed) {
          const vId = document.getElementById('vehicleSelect').value;
          const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 1;
          perPersonPrice = vCap > 0 ? (totalToPay / vCap) : 0;
      }

      if (isCustomRoute) {
          document.getElementById('breakdownPerPerson').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';`;

if (regex1.test(text)) {
    text = text.replace(regex1, newCode1);
    fs.writeFileSync(path, text, 'utf8');
    console.log('done fixing per person variable');
} else {
    console.log('regex1 not found');
}

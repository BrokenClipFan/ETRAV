const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// The original calculateTotal has this block at the end:
/*
            document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
                `Joiner / Open Group (${headsCount} of ${currentPaxLimit} slots)` :
                'Private / Exclusive Group';

            document.getElementById('breakdownHeads').innerText = isJoinerAllowed ?
                `${headsCount} people (@ ${formatCurrency(perHeadRate)} each)` :
                `${headsCount} people (splitting the total)`;

            document.getElementById('breakdownPerPerson').innerText = `${formatCurrency(costPerPerson)} / person`;
            document.getElementById('modalTotalPrice').innerText = formatCurrency(totalToPay);
            document.getElementById('modalDownpaymentPrice').innerText = formatCurrency(downpaymentRequired);
*/

const searchStr = /document\.getElementById\('breakdownBase'\)\.innerText = isJoinerAllowed \?[\s\S]*?document\.getElementById\('modalDownpaymentPrice'\)\.innerText = formatCurrency\(downpaymentRequired\);/m;

const replacement = `
        let openSlotsStr = '';
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
              'Private / Exclusive Group';

        document.getElementById('breakdownHeads').innerText = \`\$\{headsCount\} people\`;
        const vehicleName = document.getElementById('selectedVehicleName').innerText;
        document.getElementById('breakdownVehicle').innerText = vehicleName;
        
        let perPersonPrice = headsCount > 0 ? (totalToPay / headsCount) : 0;
        if (isJoinerAllowed) {
            const vId = document.getElementById('vehicleSelect').value;
            const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 1;
            perPersonPrice = vCap > 0 ? (totalToPay / vCap) : 0;
        }
        
        const depositContainer = document.getElementById('depositContainer');
        if (isCustomRoute) {
            document.getElementById('breakdownPackagePrice').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
            document.getElementById('breakdownPerPerson').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
            document.getElementById('modalTotalPrice').innerHTML = '<span class="text-warning">To Be Quoted</span>';
            if (depositContainer) depositContainer.classList.add('d-none');
        } else {
            document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('breakdownPerPerson').innerHTML = '&#8369;' + perPersonPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / person';
            document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('modalDownpaymentPrice').innerHTML = '&#8369;' + downpaymentRequired.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (depositContainer) depositContainer.classList.remove('d-none');
        }
`;

if (searchStr.test(text)) {
    text = text.replace(searchStr, replacement);
    fs.writeFileSync(path, text, 'utf8');
    console.log('calculateTotal completely fixed!');
} else {
    console.log('calculateTotal block not found!');
}

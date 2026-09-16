const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/focusOnPackageRoute\(activePackageId\);\n\s*\}\n\s*\}\);\n\s*\}\n\s*\}/g, 
`focusOnPackageRoute(activePackageId);\n                }\n            });\n        }`);

// Wait, let's just do a simpler replace.
const bug1 = `focusOnPackageRoute(activePackageId);
                }
            });
        }
        }`;
const fix1 = `focusOnPackageRoute(activePackageId);
                }
            });
        }`;
text = text.replace(bug1, fix1); // fix addPlace

const bug2 = `focusOnPackageRoute(activePackageId);
                }
            });
        }
        }`;
text = text.replace(bug2, fix1); // fix removePlace

const bug3 = `focusOnPackageRoute(activePackageId);
            });
        }
        }`;
const fix3 = `focusOnPackageRoute(activePackageId);
            });
        }`;
text = text.replace(bug3, fix3); // fix addCustom

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing brackets');

// Stocker la liste des noms de fichiers pour une utilisation ultérieure
let jsFiles = [];

function loadJsFiles() {
    fetch('list-js-files.php')
      .then(response => response.text()) // Utiliser text() pour voir la réponse brute
      .then(text => {
        // Afficher la réponse brute
        //console.log('Réponse brute:', text);
  
        // Essayer de la convertir en JSON
        return JSON.parse(text);
      })
      .then(files => {
        jsFiles = files;
        console.log('Fichiers JS chargés:', jsFiles);
      })
      .catch(error => {
        console.error('Erreur lors du chargement des fichiers JS:', error);
        document.getElementById('contentPage').innerHTML = 'Une erreur s\'est produite il faut actualiser la page';
      });
  }

window.node = function(funcName, params = {}) {
  // Vérifier si le fichier existe dans la liste
  if (!jsFiles.includes(funcName)) {
     console.error(`Le fichier ${funcName}.js n'existe pas`);
   // console.info(jsFiles);
    return;
  }

  const filePath = `./${funcName}.js`;

  import(filePath)
    .then(module => {
      if (module[funcName]) {
        module[funcName](params);
      } else {
        console.error(`La fonction ${funcName} n'est pas trouvée dans le module`);
      }
    })
    .catch(err => {
      console.error(`Une erreur est survenue lors du chargement du module: ${err}`);
    });
};

loadJsFiles(); // Charge la liste des fichiers JS au chargement de la page

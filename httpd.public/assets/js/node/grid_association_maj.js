import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';

const fetcher = new FetchHandler(true);
export function grid_association_maj(param){

    const { formId,association_id } = param;
    var formulaire = document.getElementById(formId);

    // Fonction pour récupérer tous les champs du formulaire
    function getFormFields(form) {
        var fields = [];
        // Parcourir tous les éléments du formulaire
        for (var i = 0; i < form.elements.length; i++) {
            var field = form.elements[i];
            // Ajouter l'élément au tableau si ce n'est pas un bouton
            if (field.type !== 'submit') {
                fields.push({
                    type: field.type,
                    name: field.name,
                    value: field.value,
                    disabled: field.disabled
                });
            }
        }
        return fields;
    }
    var formFields = getFormFields(formulaire);

    const data = {
        node: "action",
        action: "association_maj_save",
        form_field : formFields,
        association_id : association_id
      };
      const fetchHandler = new FetchHandler(true);
      fetchHandler.sendRequest(data)
      .then(response => {
          console.log(response);
          if(response.success == true){
              const message = response.message;
              const options = {
              position: 'top-right', 
              actionButton: false,    
              closeButton: true,     
              //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
              delay: 2000
              };
              const myToast = new Toast(message, options);
             
          }else{
              const message = "Une erreur s'est produite "+response.message;
              const options = {
              position: 'top-right', 
              actionButton: false,    
              closeButton: true,     
              //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
              delay: 4000
              };
              const myToast = new Toast(message, options);
          }
      })
      .catch(error => {
          console.error("Erreur lors de l'envoi de la requête:", error);
      });
}
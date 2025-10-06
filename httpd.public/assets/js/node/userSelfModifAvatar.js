import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function userSelfModifAvatar(params){
    const { elementId } = params;

    const fileInput = document.getElementById(elementId);
    const avatarImage = document.getElementById('avatarImg'); // Utilisez l'ID pour sélectionner l'élément img

    // Mettre à jour l'image de l'avatar immédiatement après la sélection du fichier
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarImage.src = e.target.result;
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
   
    const fetchHandler = new FetchHandler(true);

  
    // Créer un objet FormData à partir de l'élément form
    const formData = new FormData();
    formData.append("avatar", fileInput.files[0]);
  
    // Spécifiez des paramètres supplémentaires si nécessaire
    formData.append("node", "action");
    formData.append("module_personnal", "true");
    formData.append("action", "user_self_modif_avatar");
  
    
    fetchHandler.sendRequest(formData)
        .then(response => {
            console.log(response);
            if(response.success == true){
                const message = "Modification enregistrée";
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
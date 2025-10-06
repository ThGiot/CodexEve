function loadScript() {
    return new Promise((resolve, reject) => {
      if (typeof segmentsCalculator !== 'undefined') {
        // Si la bibliothèque est déjà chargée, résolvez immédiatement la promesse
        resolve();
        return;
      }
  
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/gh/TwilioDevEd/message-segment-calculator/docs/scripts/segmentsCalculator.js';
      script.crossOrigin = 'anonymous';
      script.onload = () => resolve();
      script.onerror = () => reject(new Error('Script loading failed'));
      document.body.appendChild(script);
    });
  }
  
  export function connectGetSegment(params) {
    const { textareaId } = params;
    const messageContent = document.getElementById(textareaId).value;
  
    loadScript().then(() => {
      console.log(messageContent);
      // Utilisez la bibliothèque segmentsCalculator comme nécessaire
      const segmentedMessage = new SegmentedMessage(messageContent);
     console.log(segmentedMessage.encodingName); // "GSM-7"
     document.getElementById('nbSegment').value=(segmentedMessage.segmentsCount); // "1"
      // Vous devrez peut-être ajuster cette partie en fonction de l'API exacte de la bibliothèque
    }).catch(error => {
      console.error(error);
    });
  }
  
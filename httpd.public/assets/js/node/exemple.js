export function calculer(params) {
    const { a, b, operation } = params;
  
    switch (operation) {
      case 'add':
        alert(`La somme de ${a} et ${b} est ${a + b}`);
        break;
      case 'subtract':
        alert(`La différence entre ${a} et ${b} est ${a - b}`);
        break;
      default:
        alert('Opération non reconnue');
    }
  }

  /*
  <button onclick="node('calculer', {a: 5, b: 3, operation: 'add'})">Additionner</button>
  <button onclick="node('calculer', {a: 5, b: 3, operation: 'subtract'})">Soustraire</button>

  */
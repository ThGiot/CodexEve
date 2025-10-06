<style>

/* Basic Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Main container */
.thor-container {
  font-family: 'Arial', sans-serif;
  color: #333;
  max-width: 1200px;
  margin: auto;
}

/* Header styles */
.thor-header {
  background-color: #444; /* Darker header background */
  color: white;
  padding: 40px 20px;
  text-align: center;
  border-radius: 8px;
  margin-bottom: 20px; /* Space between header and event info */
}

.thor-header h1 {
  color: white;
  margin-bottom: 15px;
  font-size: 2.5rem; /* Larger font for title */
}

.thor-header p {
  color: #ddd; /* Lighter color for subtitle */
  font-size: 1.2rem;
  opacity: 0.9;
}

/* Event info styles */
.thor-event-info {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background: #f9f9f9; /* Light grey background */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
  border-radius: 8px;
  margin-bottom: 20px; /* Space between event info and schedule */
}

.thor-event-details, .thor-event-actions {
  padding-right: 20px; /* Padding between columns */
}

.thor-event-details p, .thor-event-actions p {
  color: #333;
  margin-bottom: 10px;
}

.thor-event-details a {
  color: #0275d8;
  text-decoration: underline;
}

/* Schedule styles */
.thor-schedule {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  padding: 20px 20px 0; /* Enlever le padding en bas de la section */
  gap: 10px;
 
}

.thor-schedule-block {
  flex: 1;
  min-width: 200px;
  max-width: calc(30% - 5px);
  background-color: #fff;
  padding: 15px;
  margin-bottom: 0; /* Réduire ou enlever la marge en bas des blocks */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  border-radius: 8px;
}
.thor-schedule-info {
  text-align: center;
  color: #333;
  font-size: 1rem;
  margin-top: 10px; /* Réduire l'espace au-dessus de l'info */
  padding: 10px 0;
  background: #f5f5f5;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  width: calc(100% - 40px); /* Ajuster si nécessaire */
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 20px; /* Espace en dessous de l'info, ajuster selon le besoin */
}
.thor-schedule-title {
  font-size: 1.1em;
  font-weight: bold;
  color: #444;
  margin-bottom: 10px;
}
.thor-schedule-title .btn {
  margin-right: 5px; /* Espace entre les boutons */
  padding: 5px 5px; /* Padding à l'intérieur des boutons */
}

.thor-schedule-title .btn span[data-feather] {
  margin-right: -8px; /* Ajustement fin de l'espace autour des icônes */
}
.thor-schedule-time {
  color: #555;
  margin-bottom: 15px;
}

.thor-personnel {
  margin-bottom: 15px;
}

.thor-person {
  display: block;
  color: #444;
  font-weight: bold;
  margin: 5px 0;
}
.thor-person .btn {
  margin-right: 5px; /* Espace entre les boutons */
  padding: 5px 10px; /* Padding à l'intérieur des boutons */
}

.thor-person .btn span[data-feather] {
  margin-right: -5px; /* Ajustement fin de l'espace autour des icônes */
}
.thor-footer {
  text-align: center;
  color: #444;
}

/* Responsive adjustments for smaller screens */
@media (max-width: 768px) {
  .thor-event-info {
    flex-direction: column;
  }
  
  .thor-event-details,
  .thor-event-actions {
    width: 100%;
    padding-right: 0;
  }

  .thor-schedule {
    flex-direction: column;
  }
  
  .thor-schedule-block {
    width: 100%;
    max-width: 120%;
    margin: 10px 0;
  }
}

/* Additional styles for icons */
.thor-person:before {
  content: '👤'; /* Placeholder icon, replace with actual icons */
  margin-right: 5px;
}
</style>
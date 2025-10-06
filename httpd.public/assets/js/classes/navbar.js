class NavigationBar {
    constructor(navData) {
      this.navData = navData;
    }
  
    render() {
      let html = '';
      
      html += '<div class="collapse navbar-collapse" id="navbarVerticalCollapse">';
      html += '<div class="navbar-vertical-content">';
      html += '<ul class="navbar-nav flex-column" id="navbarVerticalNav">';
  
      this.navData.forEach(navItem => {
        html += '<li class="nav-item">';
  
        if (navItem.label) {
          html += `<p class="navbar-vertical-label">${navItem.label}</p>`;
          html += '<hr class="navbar-vertical-line" />';
        }
  
        navItem.pages.forEach(page => {
          html += '<div class="nav-item-wrapper">';
          
          if (page.subpages) {
            let pageNameSlug = page.name.replace(/ /g, '-').toLowerCase();
  
            html += `<a class="nav-link dropdown-indicator label-1" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-${pageNameSlug}">`;
            html += `<div class="d-flex align-items-center"><div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div><span class="nav-link-icon"><span class="${page.icon}"></span></span></span><span class="nav-link-text">${page.name}</span></div></a>`;
  
            html += `<div class="parent-wrapper label-1"><ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-${pageNameSlug}">`;
            html += `<li class="collapsed-nav-item-title d-none">${page.name}</li>`;
            
            page.subpages.forEach(subpage => {
              html += `<li class="nav-item"><a class="nav-link" onclick="getContent('${subpage.link}')" data-bs-toggle="" aria-expanded="false"><div class="d-flex align-items-center"><span class="nav-link-text">${subpage.name}</span></div></a></li>`;
            });
  
            html += '</ul></div>';
  
          } else {
            html += `<a class="nav-link label-1" role="button" data-bs-toggle="" aria-expanded="false" onclick="getContent('${page.link}')">`;
            html += `<div class="d-flex align-items-center"><span class="nav-link-icon"><span class="${page.icon}"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">${page.name}</span></span></div></a>`;
          }
  
          html += '</div>';  // nav-item-wrapper
        });
  
        html += '</li>';  // nav-item
      });
  
      html += '</ul>';  // navbar-nav flex-column
      html += '</div>';  // navbar-vertical-content
      html += '</div>';  // navbar-collapse
      html += '<div class="navbar-vertical-footer">';
      html += '<button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center"><span class="uil uil-left-arrow-to-left fs-0"></span><span class="uil uil-arrow-from-right fs-0"></span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button>';
      html += '</div>';  // navbar-vertical-footer
  
      // Insert the newly created HTML into the navbar element
      document.getElementById('navBar').innerHTML = html;
    }
}

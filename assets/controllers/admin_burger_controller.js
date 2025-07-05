import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'sidebar', 'overlay'];
    
    connect() {
        console.log('Admin burger menu controller connected');
        this.boundHandleEscape = this.handleEscape.bind(this);
        this.boundHandleResize = this.handleResize.bind(this);
        
        // Ajouter les event listeners globaux
        document.addEventListener('keydown', this.boundHandleEscape);
        window.addEventListener('resize', this.boundHandleResize);
    }
    
    disconnect() {
        console.log('Admin burger menu controller disconnected');
        
        // Nettoyer les event listeners globaux
        document.removeEventListener('keydown', this.boundHandleEscape);
        window.removeEventListener('resize', this.boundHandleResize);
        
        // Fermer le menu si ouvert
        this.closeMenu();
    }
    
    toggle(event) {
        event.preventDefault();
        console.log('Admin burger menu toggle clicked');
        
        const isOpen = this.buttonTarget.classList.contains('open');
        
        if (isOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }
    
    closeOnOverlay(event) {
        console.log('Admin overlay clicked - closing menu');
        this.closeMenu();
    }
    
    openMenu() {
        console.log('Opening admin menu');
        this.buttonTarget.classList.add('open');
        this.sidebarTarget.classList.add('open');
        this.overlayTarget.classList.add('open');
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
    }
    
    closeMenu() {
        console.log('Closing admin menu');
        this.buttonTarget.classList.remove('open');
        this.sidebarTarget.classList.remove('open');
        this.overlayTarget.classList.remove('open');
        
        // Restaurer le scroll du body
        document.body.style.overflow = '';
    }
    
    handleEscape(event) {
        if (event.key === 'Escape' && this.sidebarTarget.classList.contains('open')) {
            console.log('ESC key pressed - closing admin menu');
            this.closeMenu();
        }
    }
    
    handleResize() {
        // Fermer le menu automatiquement sur grand écran
        if (window.innerWidth >= 768 && this.sidebarTarget.classList.contains('open')) {
            console.log('Window resized to desktop - closing admin menu');
            this.closeMenu();
        }
    }
} 
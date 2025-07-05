import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'overlay', 'closeButton'];
    
    connect() {
        console.log('Burger menu controller connected');
        console.log('Available targets:', this.targets);
        console.log('Button target available:', this.hasButtonTarget);
        console.log('Overlay target available:', this.hasOverlayTarget);
        console.log('Close button target available:', this.hasCloseButtonTarget);
        
        if (this.hasButtonTarget) {
            console.log('Button target element:', this.buttonTarget);
        }
        if (this.hasOverlayTarget) {
            console.log('Overlay target element:', this.overlayTarget);
        }
        
        this.boundHandleEscape = this.handleEscape.bind(this);
        this.boundHandleResize = this.handleResize.bind(this);
        
        // Ajouter les event listeners globaux
        document.addEventListener('keydown', this.boundHandleEscape);
        window.addEventListener('resize', this.boundHandleResize);
    }
    
    disconnect() {
        console.log('Burger menu controller disconnected');
        
        // Nettoyer les event listeners globaux
        document.removeEventListener('keydown', this.boundHandleEscape);
        window.removeEventListener('resize', this.boundHandleResize);
        
        // Fermer le menu si ouvert
        this.closeMenu();
    }
    
    toggle(event) {
        event.preventDefault();
        console.log('Burger menu toggle clicked');
        
        const isOpen = this.buttonTarget.classList.contains('open');
        
        if (isOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }
    
    close(event) {
        event.preventDefault();
        console.log('Close button clicked');
        this.closeMenu();
    }
    
    closeOnOverlay(event) {
        // Fermer seulement si on clique sur l'overlay lui-même
        if (event.target === this.overlayTarget) {
            console.log('Overlay clicked - closing menu');
            this.closeMenu();
        }
    }
    
    openMenu() {
        console.log('Opening menu');
        console.log('Button target:', this.buttonTarget);
        console.log('Overlay target:', this.overlayTarget);
        
        this.buttonTarget.classList.add('open');
        this.overlayTarget.classList.add('open');
        
        console.log('Menu opened - classes added');
        console.log('Button classes:', this.buttonTarget.className);
        console.log('Overlay classes:', this.overlayTarget.className);
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
    }
    
    closeMenu() {
        console.log('Closing menu');
        this.buttonTarget.classList.remove('open');
        this.overlayTarget.classList.remove('open');
        
        // Restaurer le scroll du body
        document.body.style.overflow = '';
    }
    
    handleEscape(event) {
        if (event.key === 'Escape' && this.overlayTarget.classList.contains('open')) {
            console.log('ESC key pressed - closing menu');
            this.closeMenu();
        }
    }
    
    handleResize() {
        // Fermer le menu automatiquement sur grand écran
        if (window.innerWidth > 1200 && this.overlayTarget.classList.contains('open')) {
            console.log('Window resized to desktop - closing menu');
            this.closeMenu();
        }
    }
} 
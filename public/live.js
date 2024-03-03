class Live extends HTMLElement {
    static zoom = 10
    static position = null
    map

    connectedCallback() {
        if (this.querySelector('#map')) {
            const lat = parseFloat('' + this.dataset.lat)
            const lng = parseFloat('' + this.dataset.lng)

            this.map = L.map('map');
            if (Live.position) {
                this.map.setView(Live.position.getCenter(), Live.zoom)
            } else {
                this.map.setView({lat: lat, lng: lng}, Live.zoom)
            }
            this.map.on('zoom', () => {
                Live.zoom = this.map.getZoom()
            })
            this.map.on('move', () => {
                Live.position = this.map.getBounds();
            })
            this.map.attributionControl.setPrefix('')

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 15,}).addTo(this.map)

            L.marker([lat, lng]).addTo(this.map)
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    customElements.define('nicemobil-live', Live)
});


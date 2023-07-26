import "leaflet/dist/leaflet.css"
import "leaflet/dist/leaflet.js"
import iconUrl from "leaflet/dist/images/marker-icon.png"
import shadowUrl from "leaflet/dist/images/marker-shadow.png"

class Maps {
	constructor(lat, lng, zoom, div) {
		this.coords = [lat, lng]
		this.zoom = zoom
		this.div = div
	}
}
class GoogleMap {
	#map
	#maps = []
	constructor() {
		document.querySelectorAll(".acf-map").forEach(el => {
			this._loadMap(el)
		})
	}
	// load map
	_loadMap(el) {
		const markers = el.querySelectorAll(".marker")
		markers.forEach(x => {
			const newMap = new Maps(
				+x.getAttribute("data-lat"),
				+x.getAttribute("data-lng"),
				+x.getAttribute("data-zoom"),
				x
			)
			this.#maps.push(newMap)
		})
		//  map
		this.#map = L.map("map").setView(
			this.#maps[0]["coords"],
			this.#maps[0]["zoom"]
		)
		L.tileLayer("https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png", {
			attribution:
				'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		}).addTo(this.#map)

		this.#maps.forEach(x => this._renderMarkers(x))
	}
	// render marker
	_renderMarkers(marker) {
		L.marker(marker.coords, {
			icon: L.icon({
				iconUrl,
				shadowUrl,
			}),
		})
			.addTo(this.#map)
			.bindPopup(
				L.popup({
					minWidth: 100,
					maxWidth: 250,
					autoClose: false,
					closeOnClick: false,
					className: "example",
				})
			)
			.setPopupContent(marker.div)
			.openPopup()
	}
}

export default new GoogleMap()

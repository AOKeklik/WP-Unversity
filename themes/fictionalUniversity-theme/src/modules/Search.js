import icons from "../../images/icons.svg"
class View {
	_data
	render(data, render = true) {
		if (!data || (Array.isArray(data) && data.length === 0))
			return this.renderError()
		this._data = data
		const markup = this._generateMarkup()
		if (!render) return markup
		this._clear()
		this._parentElement.insertAdjacentHTML("afterbegin", markup)
	}
	renderSpinner() {
		const markup = '<div class="spinner-loader"></div>'
		this._clear()
		this._parentElement.insertAdjacentHTML("afterbegin", markup)
	}
	renderError(message = this._errorMessage) {
		const markup = `
			<div class="error">
				
					<svg>
						<use href="${icons}#icon-alert-triangle"></use>
					</svg>
				
				<p>${message}</p>
			</div>
		`
		this._clear()
		this._parentElement.insertAdjacentHTML("afterbegin", markup)
	}
	_clear() {
		this._parentElement.innerHTML = ""
	}
}
const searchView = new (class SearchView extends View {
	#isOverlayOpen = false
	previousValue
	constructor() {
		super()
		this._renderSearchHtml()
		this.searchField = document.querySelector("#search-term")
		this.openButton = document.querySelectorAll(".js-search-trigger")[1]
		this.closeButton = document.querySelector(".search-overlay__close")
		this.searchOverlay = document.querySelector(".search-overlay")
		this._events()
	}
	getQuery() {
		const query = this.searchField.value
		return query
	}
	addHandlerSearch(handler) {
		this.searchField.addEventListener("keyup", handler.bind(this))
	}
	_events() {
		this.openButton.addEventListener("click", this._openOverlay.bind(this))
		this.closeButton.addEventListener(
			"click",
			this._closeOverlay.bind(this)
		)
		document.addEventListener("keydown", this._doWhenKeyPressed.bind(this))
	}
	async _openOverlay() {
		this.searchOverlay.classList.add("search-overlay--active")
		document.body.classList.add("body-no-scroll")
		this._clearInput()
		await wait(3)
		this.searchField.focus()
		this.#isOverlayOpen = true
	}
	_closeOverlay() {
		this.searchOverlay.classList.remove("search-overlay--active")
		document.body.classList.remove("body-no-scroll")
		this.#isOverlayOpen = false
	}
	_doWhenKeyPressed(e) {
		if (e.keyCode === 83 && !this.#isOverlayOpen) this._openOverlay()
		if (e.keyCode === 32 && this.#isOverlayOpen) this._closeOverlay()
	}
	_clearInput() {
		this.searchField.value = ""
	}
	_renderSearchHtml() {
		document.body.insertAdjacentHTML(
			"beforeend",
			`
				<div class="search-overlay">
					<div class="search-overlay__top">
						<div class="container">
							<i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
							<input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
							<i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
						</div>
					</div>
					
					<div class="container">
						<div id="search-overlay__results"></div>
					</div>
	
					<div class="container">
						<div class="pagination"></div>
					</div>
				</div>
			
			`
		)
	}
})()
const resultView = new (class ResultView extends View {
	_parentElement = document.querySelector("#search-overlay__results")
	_errorMessage = "Result View. There are no results to show!!"
	_generateMarkup() {
		const renderData = this._data
			.map(result => previewView.render(result, false))
			.join("")
		return `
			<h2 class="search-overlay__section-title">General Information</h2>
			<ul class="link-list min-list">
				${renderData}
			</ul>
		`
	}
})()
const previewView = new (class PreviewView extends View {
	_generateMarkup() {
		const {
			type,
			title: { rendered },
			author_name,
			link,
		} = this._data
		return `<li><a href="${link}">${rendered}</a> ${
			type === "post" ? `post by ${author_name}` : ""
		}</li>`
	}
})()
const paginationView = new (class PaginationView extends View {
	_parentElement = document.querySelector(".pagination")
	addHandlerClick(handler) {
		this._parentElement.addEventListener("click", e => {
			const btn = e.target.closest(".pagination-btn--inline")
			if (!btn) return
			const goToPage = +btn.dataset.goto

			handler(goToPage)
		})
	}
	_generateMarkup() {
		const currentPage = this._data.page
		const numPages = Math.ceil(
			this._data.results.length / this._data.resultsPerPage
		)

		if (currentPage === 1 && numPages > 1)
			return `
				<button data-goto="${
					currentPage + 1
				}" class="pagination-btn--inline pagination__btn--next">
					<span>Page ${currentPage + 1}</span>
					<svg class="search__icon">
						<use href="${icons}#icon-arrow-right"></use>
					</svg>
				</button>
			`
		if (currentPage === numPages && numPages > 1)
			return `
				<button data-goto="${
					currentPage - 1
				}" class="pagination-btn--inline pagination__btn--prev">
					<svg class="search__icon">
						<use href="${icons}#icon-arrow-left"></use>
					</svg>
					<span>Page ${currentPage - 1}</span>
				</button>
			`

		if (currentPage < numPages)
			return `
				<button data-goto="${
					currentPage - 1
				}" class="pagination-btn--inline pagination__btn--prev">
					<svg class="search__icon">
						<use href="${icons}#icon-arrow-left"></use>
					</svg>
					<span>Page ${currentPage - 1}</span>
				</button>
				<button data-goto="${
					currentPage + 1
				}" class="pagination-btn--inline pagination__btn--next">
					<span>Page ${currentPage + 1}</span>
					<svg class="search__icon">
						<use href="${icons}#icon-arrow-right"></use>
					</svg>
				</button>
			`
		return ""
	}
})()
//  -----controller -----
searchView.addHandlerSearch(controllerSearchReasults)
async function controllerSearchReasults() {
	try {
		const query = searchView.getQuery()
		if (query == this.previousValue) return
		if (!query) return

		this.previousValue = query
		resultView.renderSpinner()
		await wait(3)
		await loadSearchResults(query)

		resultView.render(getSearchResultsPage())
		paginationView.render(state.search)
	} catch (err) {
		resultView.renderError(err.message)
	}
}
paginationView.addHandlerClick(controlPagination)
function controlPagination(goToPage) {
	resultView.render(getSearchResultsPage(goToPage))
	paginationView.render(state.search)
}
// ----- model -----
const state = {
	search: {
		query: "",
		results: [],
		page: 1,
		resultsPerPage: 3,
	},
}
async function loadSearchResults(query) {
	try {
		const data = await getAllData(
			"/wp-json/wp/v2/posts?search=" + query,
			"/wp-json/wp/v2/event?search=" + query
		)
		console.log(data)
		state.search.results = [...data]
		state.search.page = 1
	} catch (err) {
		throw err
	}
}
function getSearchResultsPage(page = state.search.page) {
	state.search.page = page
	const start = (page - 1) * state.search.resultsPerPage
	const end = page * state.search.resultsPerPage

	return state.search.results.slice(start, end)
}
// ----- fetch -----
async function getAllData(url1, url2) {
	try {
		const [res1, res2] = await Promise.all([
			fetch(universityData.root_url + url1, {
				method: "get",
				headers: {
					Accept: "application/json",
					"Content-Type": "application/json",
				},
			}),
			fetch(universityData.root_url + url2, {
				method: "get",
				headers: {
					Accept: "application/json",
					"Content-Type": "application/json",
				},
			}),
		])
		if (!res1.ok) throw new Error("Fail Fetch!!")
		const data = (await res1.json()).concat(await res2.json())
		return data
	} catch (err) {
		console.log(err)
		throw err
	}
}
function wait(ms) {
	console.log("wait!!")
	return new Promise((resolve, _) => setTimeout(resolve, 100 * ms))
}

// export default new Search()

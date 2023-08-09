class Like {
	constructor() {
		if (!window.location.pathname.startsWith("/professor/")) return
		this.likeBox = document.querySelector(".like-box")
		this.likeCount = document.querySelector(".like-count")
		this.events()
	}
	events() {
		this.likeBox.addEventListener(
			"click",
			this._makeBind(this.clickDispatcher)
		)
	}
	clickDispatcher(e) {
		const span = this._parentNode(e)
		const status = span.getAttribute("data-exists")
		if (status === "yes") this.deleteLike()
		else this.createLike()
	}
	async createLike() {
		try {
			const professorId = +this.likeBox.getAttribute("data-professor")
			const data = await this._fetchLike({
				type: "post",
				body: { professorId },
			})
			await this._wait(10)
			this._updateButton("create", data)
			console.log(data)
		} catch (err) {
			console.log(err.message)
		}
	}
	async deleteLike() {
		try {
			const likedId = +this.likeBox.getAttribute("data-like")
			const data = await this._fetchLike({
				type: "delete",
				body: { likedId },
			})
			this._updateButton("delete")
			console.log(data)
		} catch (err) {
			console.log(err.message)
		}
	}
	async _updateButton(n, data = null) {
		this._spinnerBtn()
		await this._wait(10)
		this._spinnerBtn(false)
		const status = n === "create"
		let count = +this.likeCount.innerHTML
		status ? count++ : count--
		this.likeCount.innerHTML = count
		this.likeBox.setAttribute("data-exists", status ? "yes" : "no")
		this.likeBox.setAttribute("data-like", status ? data : "")
	}
	_spinnerBtn(open = true) {
		const items = this.likeBox.querySelectorAll("*")
		if (open) {
			items.forEach(n => (n.style.display = "none"))
			this.likeBox.insertAdjacentHTML(
				"afterbegin",
				'<i class="fa fa-spinner fa-spin"></i>'
			)
			return
		}
		items.forEach(n => n.removeAttribute("style"))
		this.likeBox.querySelector(".fa-spinner").remove()
	}
	_parentNode(e) {
		let thisNode = e.target
		while (!thisNode.classList.contains("like-box"))
			thisNode = thisNode.parentElement
		return thisNode
	}
	_makeBind(cb) {
		return cb.bind(this)
	}
	async _fetchLike(data) {
		try {
			const features = {
				method: data.type,
				headers: {
					Accept: "application/json",
					"Content-Type": "application/json",
					"X-WP-Nonce": universityData.nonce,
				},
				credentials: "same-origin",
				body: JSON.stringify(data.body),
			}
			const res = await fetch(
				universityData.root_url + "/wp-json/university/v3/manageLike",
				features
			)
			if (!res.ok) throw new Error("Fetch Api Fail!!")
			const resData = await res.json()
			return resData
		} catch (err) {
			throw err
		}
	}
	_wait(ms) {
		return new Promise((resolve, _) => setTimeout(resolve, ms * 100))
	}
}

export default new Like()

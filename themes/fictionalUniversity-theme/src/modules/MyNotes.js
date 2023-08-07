import axios from "axios"
class MyNotes {
	#parentElement
	#message =
		"Note limit reached: delete an existing note to make room for a new one."
	#loading = false
	constructor() {
		if (!document.querySelector("#my-notes")) return
		this.alertMsg = document.querySelector(".note-limit-message")
		this.submitBtn = document.querySelector(".submit-note")
		this.myNotes = document.querySelector("#my-notes")
		this.title = document.querySelector(".new-note-title")
		this.content = document.querySelector(".new-note-body")
		this.evetns()
	}
	evetns() {
		this.myNotes.addEventListener("click", this.clickHandler.bind(this))
		this.submitBtn.addEventListener("click", this.createNote.bind(this))
	}
	clickHandler(e) {
		const el = n => e.target.classList.contains(n)
		if (el("delete-note") || el("fa-trash-o")) this.deleteNote(e)
		if (el("edit-note") || el("fa-pencil") || el("fa-times"))
			this._editNote(e)
		if (el("update-note") || el("fa-arrow-right")) this.updateNote(e)
	}
	async deleteNote(e) {
		try {
			this.#loading = true
			this.#parentElement = this._parentNode(e, "SPAN")
			this._loadingSpinner()
			await this._wait(10)
			this._renderButton("delete")
			await this._wait(3)
			const thisNote = this._parentNode(e, "LI")
			const data = await this._fetchNote(
				"/wp-json/wp/v2/note/" + thisNote.dataset.id,
				null,
				"delete"
			)
			console.log(data)
			if (data.userNoteCount < 5) this.alertMsg.innerHTML = ""
			this._fadeOutIN(thisNote, "out")
		} catch (err) {
			console.log(err)
		}
	}
	async updateNote(e) {
		try {
			const btn = this._parentNode(e, "span")
			const li = this._parentNode(e, "li")
			const body = {
				title: li.querySelector(".note-title-field").value.trim(),
				content: li.querySelector(".note-body-field").value.trim(),
				status: "publish",
			}

			const data = await this._fetchNote(
				"/wp-json/wp/v2/note/" + li.dataset.id,
				body,
				"post"
			)
			console.log(data)
			this.#parentElement = btn
			this._loadingSpinner()
			await this._wait(10)
			this._renderButton("save")
			await this._wait(3)
			this._makeNoteReadOnly(li, false)
		} catch (err) {
			console.log(err)
		}
	}
	async createNote(e) {
		try {
			this.#parentElement = this.submitBtn
			const body = {
				title: this.title.value.trim(),
				content: this.content.value.trim(),
				status: "publish",
			}
			const data = await this._fetchNote(
				"/wp-json/wp/v2/note/",
				body,
				"post"
			)
			this._loadingSpinner()
			await this._wait(10)
			this._renderButton()
			// prettier-ignore
			await this._wait(3);
			// prettier-ignore
			[this.title, this.content].forEach(el => (el.value = ""))
			this.alertMsg.innerHTML = ""
			console.log(data)
			if (data.userNoteCount > 4) {
				this.#parentElement = this.alertMsg
				this._renderMessage()
			}
			this.myNotes.insertAdjacentHTML(
				"afterbegin",
				`
				<li data-id="${data.id}" class="fade-in-calc">
					<input readonly class="note-title-field" value="${data.title.raw}">
					<span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
					<span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
					<textarea readonly class="note-body-field">${data.content.raw}</textarea>
					<span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
				</li>
			`
			)
			this._fadeOutIN(document.querySelector(`li[data-id="${data.id}"]`))
		} catch (err) {
			console.log(err)
		}
	}
	_editNote(e) {
		const thisNode = this._parentNode(e, "LI")
		if (thisNode.getAttribute("data-state") === "editable")
			this._makeNoteReadOnly(thisNode)
		else this._makeNoteEditable(thisNode)
	}
	async _makeNoteReadOnly(el, isBtnAppeare = true) {
		const btn = el.querySelector(".edit-note")
		const title = el.querySelector(".note-title-field")
		const content = el.querySelector(".note-body-field")
		const updateBtn = el.querySelector(".update-note")

		if (isBtnAppeare) {
			this.#parentElement = btn
			this._loadingSpinner()
			await this._wait(10)
			// prettier-ignore
			this._renderButton("edit");
		}
		// prettier-ignore
		[title, content].forEach(n => {
			n.setAttribute("readonly", true)
			n.classList.remove("note-active-field")
		})
		updateBtn.classList.remove("update-note--visible")
		el.setAttribute("data-state", "cancel")
	}
	async _makeNoteEditable(el) {
		const btn = el.querySelector(".edit-note")
		const title = el.querySelector(".note-title-field")
		const content = el.querySelector(".note-body-field")
		const updateBtn = el.querySelector(".update-note")
		this.#parentElement = btn
		this._loadingSpinner()
		await this._wait(10)
		// prettier-ignore
		this._renderButton("times");
		// prettier-ignore
		[title, content].forEach(n => {
			n.removeAttribute("readonly")
			n.classList.add("note-active-field")
		})
		this._setCaretAtEnd(title)
		updateBtn.classList.add("update-note--visible")
		el.setAttribute("data-state", "editable")
	}
	_setCaretAtEnd(elem) {
		var elemLen = elem.value.length

		elem.selectionStart = elemLen
		elem.selectionEnd = elemLen
		elem.focus()
	}
	_loadingSpinner() {
		this.#parentElement.innerHTML = ""
		this.#parentElement.insertAdjacentHTML(
			"afterbegin",
			`<i class="fa fa-spinner fa-spin"></i>`
		)
	}
	_renderButton(type) {
		console.log(this.#parentElement)
		console.log(type)
		this.#parentElement.innerHTML = ""
		this.#parentElement.insertAdjacentHTML(
			"afterbegin",
			type === "edit"
				? '<i class="fa fa-pencil" aria-hidden="true"></i> Edit'
				: type === "times"
				? `<i class="fa fa-times" aria-hidden="true"></i> Cancel`
				: type === "delete"
				? '<i class="fa fa-trash-o" aria-hidden="true"></i> Delete'
				: type === "save"
				? '<i class="fa fa-arrow-right" aria-hidden="true"></i> Save'
				: "Create Note"
		)
	}
	async _renderMessage(messsage = this.#message) {
		this.#parentElement.classList.add("active")
		this._loadingSpinner()
		await this._wait(10)
		this.#parentElement.innerHTML = ""
		this.#parentElement.insertAdjacentHTML("afterbegin", messsage)
	}
	_parentNode(e, el) {
		let thisNode = e.target
		while (thisNode.tagName !== el.toUpperCase())
			thisNode = thisNode.parentNode
		return thisNode
	}
	async _fadeOutIN(li, type = null) {
		const finalHeight = `${li.offsetHeight}px`
		await this._wait(3)
		if (type === "out") {
			li.classList.add("fade-out")
			li.style.height = finalHeight
		} else {
			li.style.height = "0px"
		}
		await this._wait(3)
		if (type === "out") li.remove()
		else {
			li.classList.remove("fade-in-calc")
			li.style.height = finalHeight
			await this._wait(3)
			li.style.removeProperty("height")
		}
	}
	async _fetchNote(url, body = null, type = "get") {
		try {
			axios.defaults.headers.common["X-WP-Nonce"] = universityData.nonce
			const features = {
				method: type,
				headers: {
					"Content-Type": "application/json",
					Accept: "application/json",
					"X-WP-Nonce": universityData.nonce,
				},
				...(body &&
					type === "post" && {
						body: JSON.stringify(body),
						credentials: "same-origin",
					}),
			}
			console.log(features)
			const res = await fetch(universityData.root_url + url, features)
			if (!res.ok) throw new Error("Error Fetch Api!!")
			const data = await res.json()
			return data
		} catch (err) {
			throw err
		}
	}
	_wait(ms) {
		return new Promise(function (resolve, _) {
			setTimeout(() => resolve(), ms * 100)
		})
	}
}

export default new MyNotes()

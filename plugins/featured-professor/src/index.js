import "./index.scss"
import { useSelect } from "@wordpress/data"
import { useState, useEffect } from "react"

wp.blocks.registerBlockType("ourplugin/featured-professor", {
	title: "Professor Callout",
	description:
		"Include a short description and link to a professor of your choice",
	icon: "welcome-learn-more",
	category: "common",
	example: {
		attributes: {
			profId: 134,
		},
	},
	attributes: {
		profId: { type: "string" },
	},
	edit: EditComponent,
	save(props) {
		return null
	},
})

function EditComponent({ attributes: { profId }, setAttributes }) {
	const [thePreview, setThePreview] = useState("")
	const allProfessors = useSelect(select =>
		select("core").getEntityRecords("postType", "professor", {
			per_page: -1,
		})
	)

	useEffect(() => {
		if (profId) {
			updateTheMeta()
			const go = async () => {
				const res = await findProf(
					"/wp-json/featuredProfessor/v3/getHTML?profId=" + profId
				)
				setThePreview(res)
			}
			go()
		}
	}, [profId])

	useEffect(() => {
		return () => {
			updateTheMeta()
		}
	}, [])

	const updateTheMeta = () => {
		const featuredprofessor = wp.data
			.select("core/block-editor")
			.getBlocks()
			.filter(block => block.name === "ourplugin/featured-professor")
			.map(block => block.attributes.profId)
			.filter((item, index, arr) => {
				return arr.indexOf(item) === index
			})
		wp.data
			.dispatch("core/editor")
			.editPost({ meta: { featuredprofessor } })
		console.log(featuredprofessor)
	}

	const handleChangeProfessor = e => setAttributes({ profId: e.target.value })
	const renderProfessors =
		allProfessors &&
		allProfessors.map(prof => {
			return (
				<option value={prof.id} selected={prof.id === +profId}>
					{prof.title.rendered}
				</option>
			)
		})
	return (
		<div className="featured-professor-wrapper">
			{renderProfessors ? (
				<div className="professor-select-container">
					<select onChange={handleChangeProfessor}>
						<option value="">Select a professor</option>
						{renderProfessors}
					</select>
				</div>
			) : (
				"Loading..."
			)}
			<div dangerouslySetInnerHTML={{ __html: thePreview }}></div>
		</div>
	)
}
async function findProf(url) {
	const features = {
		method: "get",
		headers: {
			Accept: "application/json",
			"Content-Type": "application/json",
		},
		credentials: "same-origin",
	}
	const res = await fetch(featuredProfessorData.root_url + url, features)
	const result = await res.json()
	return result
}

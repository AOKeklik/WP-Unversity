import "./index.scss"
import {
	Flex,
	FlexBlock,
	FlexItem,
	TextControl,
	Button,
	Icon,
	PanelBody,
	PanelRow,
} from "@wordpress/components"
import {
	InspectorControls,
	BlockControls,
	AlignmentToolbar,
} from "@wordpress/block-editor"
import { ChromePicker } from "react-color"

// prettier-ignore
(function () {
	let locked = false

	wp.data.subscribe(function () {
		const results = wp.data
			.select("core/block-editor")
			.getBlocks()
			.filter(
				n =>
					n.name === "ourplugin/are-you-paying-attention" &&
					n.attributes.correctAnswer === undefined
			)
		if (results.length && !locked) {
			locked = true
			wp.data.dispatch("core/editor").lockPostSaving("noanswer")
		}
		if (!results.length && locked) {
			locked = false
			wp.data.dispatch("core/editor").unlockPostSaving("noanswer")
		}
	})
})()

wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
	title: "Are You Paying Attention?",
	icon: "smiley",
	category: "common",
	description: "Give your audience a chance to prove their comprehension.",
	attributes: {
		question: { type: "string" },
		answers: { type: "array", default: [""] },
		correctAnswer: { type: "number", default: undefined },
		bgColor: { type: "string", default: "#EBEBEB" },
		theAlignment: { type: "string", default: "left" },
	},
	example: {
		attributes: {
			question: "What is my name?",
			correctAnswer: 3,
			answers: ["Meowsalot", "Barksalot", "Purrsloud", "Onur"],
			theAlignment: "center",
			bgColor: "#CFE8F1",
		},
	},
	edit: EditComponent,
	save() {
		return null
	},
})

function EditComponent(props) {
	const { setAttributes } = props
	const { question, answers, correctAnswer, bgColor, theAlignment } =
		props.attributes
	const handleChangeAlignment = val => setAttributes({ theAlignment: val })
	const handleChangeBgColor = val => setAttributes({ bgColor: val.hex })
	const handleChangeQuestion = val => setAttributes({ question: val })
	const handleDeleteAnswer = index => {
		const updatedAnswers = answers.concat([]).filter((_, i) => i !== index)
		setAttributes({ answers: updatedAnswers })

		if (index !== correctAnswer) return
		setAttributes({ correctAnswer: undefined })
	}
	const handleChangeAnswer = (index, val) => {
		const tempAnswers = answers.concat([])
		tempAnswers[index] = val
		setAttributes({ answers: tempAnswers })
	}
	const handleSelectCorrectAnswer = index => {
		if (correctAnswer === index) {
			setAttributes({ correctAnswer: undefined })
			return
		}
		setAttributes({ correctAnswer: index })
	}
	console.log(correctAnswer)
	const handleSubmitForm = () =>
		setAttributes({ answers: answers.concat([""]) })
	return (
		<div
			className="paying-attention-edit-block"
			style={{ backgroundColor: bgColor, textAlign: theAlignment }}
		>
			<BlockControls>
				<AlignmentToolbar
					value={theAlignment}
					onChange={handleChangeAlignment}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody>
					<PanelRow>
						<ChromePicker
							color={bgColor}
							onChangeComplete={handleChangeBgColor}
							disableAlpha
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<TextControl
				label="Question:"
				value={question}
				onChange={handleChangeQuestion}
				style={{ fontSize: "20px" }}
			/>
			<p style={{ fontSize: "13px", margin: "20px 0 8px 0" }}>Answers:</p>
			{answers.map((answer, index) => {
				return (
					<Flex>
						<FlexBlock>
							<TextControl
								value={answer}
								onChange={val => handleChangeAnswer(index, val)}
							/>
						</FlexBlock>
						<FlexItem>
							<Button>
								<Icon
									onClick={() =>
										handleSelectCorrectAnswer(index)
									}
									icon={
										correctAnswer === index
											? "star-filled"
											: "star-empty"
									}
									className="mark-as-correct"
								/>
							</Button>
						</FlexItem>
						<FlexItem>
							<Button
								onClick={() => handleDeleteAnswer(index)}
								isLink
								className="attention-delete"
							>
								Delete
							</Button>
						</FlexItem>
					</Flex>
				)
			})}
			<Button isPrimary onClick={handleSubmitForm}>
				Add another answer
			</Button>
		</div>
	)
}

/**
 * @module Save
 * @description Gift Certificate Form block save method.
 */

const saveBlock = (props) => {
	const { shortcode } = props.attributes;


	return (
		<div
			className={props.className}
		>
			{ shortcode }
		</div>
	);
};

export default saveBlock;

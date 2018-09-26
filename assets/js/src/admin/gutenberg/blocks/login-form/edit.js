/**
 * @module Edit
 * @description Login Form block edit method.
 */


import { ADMIN_IMAGES } from '../../../config/wp-settings';
import { GUTENBERG_LOGIN as BLOCK } from '../../config/gutenberg-settings';

const editBlock = (props) => {
	const { setAttributes } = props;
	const blockImage = `${ADMIN_IMAGES}Gutenberg-Block_LogIn.png`;

	setAttributes({
		shortcode: BLOCK.shortcode,
	});

	return [
		<h2
			className={props.className}
			key="login-form-shortcode-title"
		>
			{ BLOCK.block_html.title }
		</h2>,
		<img
			src={blockImage}
			alt={BLOCK.title}
			className={props.className}
			key="login-form-shortcode-preview"
		/>,
	];
};

export default editBlock;

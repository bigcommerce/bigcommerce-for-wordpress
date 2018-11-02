/**
 * @module Edit
 * @description Checkout block edit method.
 */


import { ADMIN_IMAGES } from '../../../config/wp-settings';
import { GUTENBERG_CHECKOUT as BLOCK } from '../../config/gutenberg-settings';

const editBlock = (props) => {
	const { setAttributes } = props;
	const blockImage = `${ADMIN_IMAGES}Gutenberg-Block_Checkout.png`;

	setAttributes({
		shortcode: BLOCK.shortcode,
	});

	return [
		<h2
			className={props.className}
			key="checkout-shortcode-title"
		>
			{ BLOCK.block_html.title }
		</h2>,
		<img
			src={blockImage}
			alt={BLOCK.title}
			className={props.className}
			key="checkout-shortcode-preview"
		/>,
	];
};

export default editBlock;

/**
 * @module Edit
 * @description Gift Certificate Balance block edit method.
 */


import { ADMIN_IMAGES } from '../../../config/wp-settings';
import { GUTENBERG_GIFT_CERTIFICATE_BALANCE as BLOCK } from '../../config/gutenberg-settings';

const editBlock = (props) => {
	const { setAttributes } = props;
	const blockImage = `${ADMIN_IMAGES}Gutenberg-Block_Gift-Cert-Balance.png`;

	setAttributes({
		shortcode: BLOCK.shortcode,
	});

	return [
		<h2
			className={props.className}
			key="gc-balance-shortcode-title"
		>
			{ BLOCK.block_html.title }
		</h2>,
		<img
			src={blockImage}
			alt={BLOCK.title}
			className={props.className}
			key="gc-balance-shortcode-preview"
		/>,
	];
};

export default editBlock;

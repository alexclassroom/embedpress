import { getIframeTitle } from '../../../GlobalCoponents/helper';
import md5 from "md5";

const save = (props) => {
	const { iframeSrc, width, height, enableLazyLoad, customPlayer, clientId } = props.attributes

	// Disable lazy loading if custom player is enabled
	const shouldLazyLoad = enableLazyLoad && !customPlayer;

	// Generate client ID hash for content protection
	const _md5ClientId = md5(clientId || '');

	return (
		<div id={`ep-gutenberg-content-${_md5ClientId}`} className="ep-gutenberg-content">
			<div
				className="ose-wistia"
				data-embed-type="Wistia">
				{shouldLazyLoad ? (
					<div
						className="ep-lazy-iframe-placeholder"
						data-ep-lazy-src={iframeSrc}
						data-ep-iframe-allowtransparency="true"
						data-ep-iframe-frameborder="0"
						data-ep-iframe-class="wistia_embed"
						data-ep-iframe-name="wistia_embed"
						data-ep-iframe-width={width}
						data-ep-iframe-height={height}
						data-ep-iframe-title={getIframeTitle(iframeSrc)}
						style={{ width: `${width}px`, height: `${height}px`, maxWidth: '100%' }}
					/>
				) : (
					<iframe
						src={iframeSrc}
						allowtransparency="true"
						frameBorder="0"
						className="wistia_embed"
						name="wistia_embed"
						width={width}
						height={height}
						title={getIframeTitle(iframeSrc)}
					></iframe>
				)}
			</div>
		</div>
	);
};

export default save;

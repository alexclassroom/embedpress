import { getIframeTitle } from '../../../GlobalCoponents/helper';
import md5 from "md5";

const save = (props) => {
	const {
		iframeSrc,
		attrs,
		enableLazyLoad,
		customPlayer,
		clientId
	} = props.attributes
	const defaultClass = "ose-twitch-presentation"
	const IframeUrl = iframeSrc + '&parent=' + embedpressGutenbergData.twitch_host;

	// Disable lazy loading if custom player is enabled
	const shouldLazyLoad = enableLazyLoad && !customPlayer;

	// Generate client ID hash for content protection
	const _md5ClientId = md5(clientId || '');

	return (
		<div id={`ep-gutenberg-content-${_md5ClientId}`} className="ep-gutenberg-content">
			<figure
				className={defaultClass}
				data-embed-type="Twitch">
				{shouldLazyLoad ? (
					<div
						className="ep-lazy-iframe-placeholder"
						data-ep-lazy-src={IframeUrl}
						data-ep-iframe-frameborder="0"
						data-ep-iframe-width="600"
						data-ep-iframe-height="450"
						data-ep-iframe-title={getIframeTitle(iframeSrc)}
						style={{ width: '600px', height: '450px', maxWidth: '100%' }}
					/>
				) : (
					<iframe
						src={IframeUrl} {...attrs}
						frameBorder="0"
						width="600"
						title={getIframeTitle(iframeSrc)}
						height="450"></iframe>
				)}
			</figure>
		</div>
	);
};

export default save;

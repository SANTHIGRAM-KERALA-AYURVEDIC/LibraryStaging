const { registerPlugin } = wp.plugins;
const { __ } = wp.i18n;
const { PluginDocumentSettingPanel, PluginSidebarMoreMenuItem, PluginSidebar } = wp.editPost;
const { PanelBody, PanelRow, Icon } = wp.components;
const { compose } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { useState, Fragment, useEffect } = wp.element;

const BoosterSidebarPanel = () => {
  const postType = wp.data.select('core/editor').getCurrentPostType();
  const allowedPostTypes = ['post', 'page'];
  if (!allowedPostTypes.includes(postType) || wp.data.select('core/editor').getCurrentPost().status != "publish") {
    return '';
  }
  return(
    <Fragment>
      <PluginSidebarMoreMenuItem target='awp-custom-sidebar'>{__('10Web Booster', 'tenweb-speed-optimizer')}</PluginSidebarMoreMenuItem>
      <PluginSidebar
        title={__('10Web Booster', 'tenweb-speed-optimizer')}>
        <BoosterPanelRow/>
      </PluginSidebar>
    </Fragment>
  );
}

const BoosterSettingPanel = () => {
  const postType = wp.data.select('core/editor').getCurrentPostType();
  const allowedPostTypes = ['post', 'page'];
  if (!allowedPostTypes.includes(postType) || wp.data.select('core/editor').getCurrentPost().status != "publish") {
    return '';
  }
  return(
    <Fragment>
      <PluginDocumentSettingPanel
        name="awp-custom-sidebar"
        title={__('10Web Booster', 'tenweb-speed-optimizer')}>
        <BoosterPanelRow />
      </PluginDocumentSettingPanel>
    </Fragment>
  );
}

const BoosterPanelRow = () => {
  const post_id = wp.data.select('core/editor').getCurrentPostId();
  const metas = wp.data.select('core/editor').getCurrentPostAttribute('meta');
  let status = 'optimized';
  if (post_id in two_speed.critical_pages) {
    if ( typeof two_speed.critical_pages[post_id]['status'] != "undefined"
        && two_speed.critical_pages[post_id]['status'] == 'in_progress' ) {
      status = 'optimizing';
    }
  } else {
    status = 'notOptimized';
  }

  if ( status == "optimized" ) {
    let panelRow = <><PanelRow>
      <Optimized metas={metas} post_id={post_id}/>
    </PanelRow></>;
    return panelRow;
  } else if ( status == "notOptimized" ) {
    return (
      <PanelRow>
        <NotOptimized post_id={post_id} />
      </PanelRow>);
  }
  else {
    return (
      <PanelRow>
        <Optimizing />
      </PanelRow>);
  }
}

const NotOptimized = ({post_id}) => {
  const [isLoading, setIsLoading] = useState(false);
  const optimize = (post_id) => {
    if (two_speed.optimize_entire_website != false) {
      window.open(two_speed.optimize_entire_website + '?two_comes_from=gutenbergAfterLimit', '_blank');
    }
    else {
      setIsLoading(true);
      fetch(two_speed.ajax_url + '?action=two_optimize_page&post_id=' + post_id + '&nonce=' + two_speed.nonce
      + '&initiator=gutenberg' )
        .then(_res => {
        });
    }
  }

  if (isLoading) {
    return (
      <Optimizing/>
    )
  } else {
    return (
      <span className="two-editor-page-speed two-notoptimized ">
      <b>{__('Optimize with 10Web Booster', 'tenweb-speed-optimizer')}</b>
      <p>{__('Optimize now to get a 90+ PageSpeed score.', 'tenweb-speed-optimizer')}</p>
      <a onClick={() => optimize(post_id)}
         data-initiator="gutenberg"
         className="two-button-green">{__('Optimize', 'tenweb-speed-optimizer')}</a></span>
    );
  }
}

const Optimizing = () => {
  return(
    <span className="two-page-speed two-optimizing">{__('Optimizing...', 'tenweb-speed-optimizer')}
      <p className="two-description">{__('Please refresh the page in 2 minutes.', 'tenweb-speed-optimizer')}</p>
    </span>
  );
}

const Optimized = ({metas,post_id}) => {
  useEffect(() => {
    const elements = document.getElementsByClassName('two-score-circle');
    for (let i = 0; i < elements.length; i++) {
      two_draw_score_circle(elements[i]);
    }
  }, []);
  const [isLoading, setIsLoading] = useState(false);
  const optimize = (post_id) => {
    setIsLoading(true);
    fetch(two_speed.ajax_url + '?action=two_optimize_page&post_id=' + post_id + '&nonce=' + two_speed.nonce
        + '&initiator=gutenberg')
        .then(_res => {
        });
  }
  let score_data;
  if ( two_speed.front_page_id == post_id ) {
    score_data = two_speed.two_front_page_speed;
  } else {
    score_data = metas.two_page_speed;
  }
  let date = 0;
  if ( score_data && !score_data['previous_score'] ) {
    return false;
  } else if ( score_data && score_data['current_score'] ) {
    if( (post_id in two_speed.critical_pages) && two_speed.critical_pages[post_id]['critical_date'] ) {
      date = two_speed.critical_pages[post_id]['critical_date'];
    } else if( score_data['current_score']['date'] ) {
      date = Date.parse(score_data['current_score']['date']);
    }
  }
  let modified_date='',re_optimize = false;
  modified_date = Date.parse(wp.data.select('core/editor').getEditedPostAttribute('modified') + 'Z')/1000;
  if ( modified_date > date && date != 0 ) {
    re_optimize = true;
  }
  const reOptimizeButton = <div className="two-score-container-reoptimize">
    <a onClick={() => optimize(post_id)}
       data-initiator="gutenberg"
       className="two-button-green">{__('Re-optimize', 'tenweb-speed-optimizer')}</a>
  </div>;
  if (isLoading) {
    return (
        <Optimizing/>
    )
  } else {
    const title = wp.data.select("core/editor").getEditedPostAttribute( 'title' );
    return (
        <div className="two-score-section two-score-section-gutenberg">
          <div
              className="two-score-container-title two-title-congrats">{__('Congrats!', 'tenweb-speed-optimizer')}</div>
          <p className="two-gutenberg-container-title"><span><b>{title}</b></span>{__(' page is successfully optimized', 'tenweb-speed-optimizer')}</p>
          <div
              className="two-score-container-title">{__('Overview of your page performance:', 'tenweb-speed-optimizer')}</div>
          <div className="two-score-container-both">
            <div className="two-score-container-old">
              <div className="two-score-header">{__('Before optimization', 'tenweb-speed-optimizer')}</div>
              <div className="two-score-mobile">
                <div className="two-score-circle two_circle_with_bg"
                     data-id="mobile"
                     data-thickness="2"
                     data-size="40"
                     data-score={metas.two_page_speed.previous_score.mobile_score}
                     data-loading-time={metas.two_page_speed.previous_score.mobile_tti}>
                  <span className="two-score-circle-animated"></span>
                </div>
                <div className="two-score-text">
                  <span className="two-score-text-name">{__('Mobile score', 'tenweb-speed-optimizer')}</span>
                  <span className="two-load-text-time">{__('Load time:', 'tenweb-speed-optimizer')} <span
                      className="two-load-time"></span>s</span>
                </div>
              </div>
              <div className="two-score-desktop">
                <div className="two-score-circle two_circle_with_bg"
                     data-id="desktop"
                     data-thickness="2"
                     data-size="40"
                     data-score={metas.two_page_speed.previous_score.desktop_score}
                     data-loading-time={metas.two_page_speed.previous_score.desktop_tti}>
                  <span className="two-score-circle-animated"></span>
                </div>
                <div className="two-score-text">
                  <span className="two-score-text-name">{__('Desktop score', 'tenweb-speed-optimizer')}</span>
                  <span className="two-load-text-time">{__('Load time:', 'tenweb-speed-optimizer')} <span
                      className="two-load-time"></span>s</span>
                </div>
              </div>
            </div>
            <div className="two-score-container-new">
              <div className="two-score-header">{__('After optimization', 'tenweb-speed-optimizer')}</div>
              <div className="two-score-mobile">
                <div className="two-score-circle two_circle_with_bg"
                     data-id="mobile"
                     data-thickness="2"
                     data-size="40"
                     data-score={metas.two_page_speed.current_score.mobile_score}
                     data-loading-time={metas.two_page_speed.current_score.mobile_tti}>
                  <span className="two-score-circle-animated"></span>
                </div>
                <div className="two-score-text">
                  <span className="two-score-text-name">{__('Mobile score', 'tenweb-speed-optimizer')}</span>
                  <span className="two-load-text-time">{__('Load time:', 'tenweb-speed-optimizer')} <span
                      className="two-load-time"></span>s</span>
                </div>
              </div>
              <div className="two-score-desktop">
                <div className="two-score-circle two_circle_with_bg"
                     data-id="desktop"
                     data-thickness="2"
                     data-size="40"
                     data-score={metas.two_page_speed.current_score.desktop_score}
                     data-loading-time={metas.two_page_speed.current_score.desktop_tti}>
                  <span className="two-score-circle-animated"></span>
                </div>
                <div className="two-score-text">
                  <span className="two-score-text-name">{__('Desktop score', 'tenweb-speed-optimizer')}</span>
                  <span className="two-load-text-time">{__('Load time:', 'tenweb-speed-optimizer')} <span
                      className="two-load-time"></span>s</span>
                </div>
              </div>
            </div>
          </div>
          {/* button is currently not used
            {re_optimize && reOptimizeButton}
            */}
        </div>
    );
  }
}

registerPlugin('booster-sidebar-panel', {
  render: BoosterSidebarPanel,
  icon: <svg className="two-speed-icon" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26">
    <g id="Group_103139" data-name="Group 103139" transform="translate(0 -0.391)">
      <path id="Path_171039" data-name="Path 171039"
            d="M.441,38.127h0a1.445,1.445,0,0,0,2.065-.037l7.924-6.038a.131.131,0,0,0,.033-.18.126.126,0,0,0-.158-.045l-9.409,4a1.426,1.426,0,0,0-.52,2.23Z"
            transform="translate(-0.028 -23.443)" fill="#fff"/>
      <path id="Path_171040" data-name="Path 171040"
            d="M5.434,48.088a1.443,1.443,0,0,1-2.063.039l0,0L.462,45.274l-.034-.029-.06-.063a1.427,1.427,0,0,1,.12-1.992,1.393,1.393,0,0,1,.4-.252L4.295,41.49,3.723,42a1.571,1.571,0,0,0-.163,2.191q.045.054.095.1l1.74,1.7a1.5,1.5,0,0,1,.039,2.1"
            transform="translate(-0.014 -30.56)" fill="#9ea3a8"/>
      <path id="Path_171041" data-name="Path 171041"
            d="M69.869,43.142h0a1.445,1.445,0,0,0-2.065.037l-7.911,6.038a.131.131,0,0,0-.033.18.126.126,0,0,0,.158.045l9.4-4a1.426,1.426,0,0,0,.52-2.23Z"
            transform="translate(-44.277 -31.469)" fill="#fff"/>
      <path id="Path_171042" data-name="Path 171042"
            d="M78,32.276a1.443,1.443,0,0,1,2.063-.039l0,0,2.907,2.851L83,35.12l.06.063a1.427,1.427,0,0,1-.12,1.992,1.393,1.393,0,0,1-.4.252l-3.407,1.448.572-.507a1.571,1.571,0,0,0,.163-2.191q-.045-.054-.095-.1l-1.742-1.7a1.5,1.5,0,0,1-.036-2.1"
            transform="translate(-57.411 -23.446)" fill="#9ea3a8"/>
      <path id="Path_171043" data-name="Path 171043"
            d="M31.607,23.5l5.172-7.19a.126.126,0,0,0,0-.176.121.121,0,0,0-.173,0l-13.2,10.025a.131.131,0,0,0-.03.18.127.127,0,0,0,.106.055h4.143a.136.136,0,0,1,.134.139.138.138,0,0,1-.025.078L22.56,33.8a.126.126,0,0,0,0,.176.121.121,0,0,0,.173,0l13.2-10.025a.131.131,0,0,0,.03-.18.127.127,0,0,0-.106-.055H31.717a.136.136,0,0,1-.134-.139.138.138,0,0,1,.025-.078"
            transform="translate(-16.668 -11.879)" fill="#9ea3a8"/>
    </g>
  </svg>
});
registerPlugin('booster-settings-panel', {
  render: BoosterSettingPanel,
  icon: ''
});

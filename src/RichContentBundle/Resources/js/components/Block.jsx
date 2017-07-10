import React from 'react';

import blockTypes from './blocktypes';

class Block extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      editing: false,
    };
  }

  clickEdit() {
    this.setState({
      editing: true
    });
  }

  render() {
    const Tag = blockTypes[this.props.block.type];
    const editClass = 'fa ' + (this.state.editing ? 'fa-times' : 'fa-pencil');

    return (
      <div>
        <div className="btn-group">
          <a className="btn btn-xs btn-default move-up"><i className="fa fa-arrow-up"></i></a>
          <a className="btn btn-xs btn-default move-down"><i className="fa fa-arrow-down"></i></a>
          <a className="btn btn-xs btn-default edit" onClick={this.clickEdit.bind(this)}>
            <i className={editClass}></i>
          </a>
          <a className="btn btn-xs btn-default remove"><i className="fa fa-trash"></i></a>
        </div>
        <Tag value={this.props.block.value} editing={this.state.editing} />
      </div>
    )
  }
}

export default Block;

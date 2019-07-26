/**
 * @module Edit
 * @description Product Components block edit method.
 */

import InstancedEdit from './instancedEdit';

const { withInstanceId } = wp.compose;

const editBlock = withInstanceId(InstancedEdit);

export default editBlock;

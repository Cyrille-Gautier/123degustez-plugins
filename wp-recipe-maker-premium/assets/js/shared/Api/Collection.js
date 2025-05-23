const collectionsEndpoint = wprmp_admin.endpoints.collections;
const savedCollectionEndpoint = wprmp_admin.endpoints.saved_collection;

import ApiWrapper from 'Shared/ApiWrapper';

export default {
    reload(id) {
        return ApiWrapper.call( `${savedCollectionEndpoint}/reload/${id}`, 'POST' );
    },
    delete(id) {
        return ApiWrapper.call( `${collectionsEndpoint}/${id}`, 'DELETE' );
    },
    update(id, collection) {
        const data = {
            collection,
        }
        return ApiWrapper.call( `${collectionsEndpoint}/${id}`, 'POST', data );
    },
};

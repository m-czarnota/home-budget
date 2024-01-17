import { library } from '@fortawesome/fontawesome-svg-core';

import { 
    faFolderPlus, 
    faFilePen, 
    faAngleRight, 
    faAngleDown, 
    faMinus, 
    faTrash,
    faFileCirclePlus,
} from '@fortawesome/free-solid-svg-icons';
import { 
    faCircleCheck,
} from '@fortawesome/free-regular-svg-icons';

library.add(
    faFolderPlus, 
    faFilePen, 
    faAngleRight, 
    faAngleDown, 
    faMinus, 
    faTrash,
    faFileCirclePlus,
);
library.add(
    faCircleCheck,
);

export default library;
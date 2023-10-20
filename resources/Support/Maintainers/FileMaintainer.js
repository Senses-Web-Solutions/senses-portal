import useEcho from '../useEcho';
import Pluralize from "pluralize";
import highlight from "../highlight";
import { nextTick } from 'vue'
const echo = useEcho();
export default class {
    constructor(data, fileableType, fileableId) {
        this.data = data;
        this.fileableType = fileableType;
        this.fileableId = fileableId;

        echo.private(`${Pluralize(this.fileableType)}.${this.fileableId}.files`)
        .listen('Files\\FileCreated', (pusherEvent) => {

                //find folder to push file into
                const folder = this.getFolderFromFileFolder(pusherEvent.file.folder, this.data); //root folder

                if(Array.isArray(folder)) {
                    folder.push(pusherEvent.file);
                }
                else {
                    folder[pusherEvent.file.id] = pusherEvent.file;
                }
                nextTick(() => {
                    this.highlightFile(pusherEvent.file.id);
                });
        })
        .listen('Files\\FileUpdated', (pusherEvent) => {
            const folder = this.getFolderFromFileFolder(pusherEvent.file.folder, null);

            if (folder) {
                const index = folder.findIndex(item => item.id === pusherEvent.file.id);
                if (index > -1 && folder[index]) {
                    folder[index] = pusherEvent.file;
                    nextTick(() => {
                        this.highlightFile(pusherEvent.file.id);
                    });
                }
            } else {
                this.data[pusherEvent.file.id] = pusherEvent.file;
            }
        })
        .listen('Files\\FileDeleted', (pusherEvent) => {
                const folder = this.getFolderFromFileFolder(pusherEvent.file.folder, null);
                if (folder) {
                    const index = folder.findIndex(item => item.id === pusherEvent.file.id);
                    if (index > -1 && folder[index]) {
                        folder.splice(index, 1);
                    }
                } else if (this.data[pusherEvent.file.id]) { //delete if not in a folder
                    delete this.data[pusherEvent.file.id];
                }
            }
        );
    }

    destroy(){
        echo.leave(`${Pluralize(this.fileableType)}.${this.fileableId}.files`);
    }

    highlightFile(fileID){
        highlight(document.querySelector("[data-file-id='" + fileID + "']"));
    }

    //take a string of folder and get array for folder
    getFolderFromFileFolder(fileFolder, defaultFolder = null) {

        if(!fileFolder) {
            return defaultFolder;
        }

        const path = fileFolder.split('/');

        if(path.length == 0) {
            return defaultFolder;
        }

        let folder = this.data;
        for(const pathFolder of path) {
            if(!folder[pathFolder]) {
                folder = defaultFolder;
                break;
            }

            folder = folder[pathFolder];
        }

        return folder;
    }


}

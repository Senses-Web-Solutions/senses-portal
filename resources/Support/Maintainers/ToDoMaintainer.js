import useEcho from '../useEcho';
import Pluralize from "pluralize";
import highlight from "../highlight";
import { nextTick } from 'vue'
const echo = useEcho();
export default class {
    constructor(data, todoableType, todoableId) {
        this.data = data;
        this.todoableType = todoableType;
        this.todoableId = todoableId;

        echo.private(`${Pluralize(this.todoableType)}.${this.todoableId}.to-dos`).listen(
            'ToDos\\ToDoCreated',
            (pusherEvent) => {
                this.data.push(pusherEvent.toDo);
                nextTick(() => {
                    this.highlightToDo(pusherEvent.toDo.id);
                });
            }
        ).listen(
            'ToDos\\ToDoUpdated',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.toDo.id);
                if (index > -1 && this.data[index]) {
                    this.data[index] = pusherEvent.toDo;
                    nextTick(() => {
                        this.highlightToDo(pusherEvent.toDo.id);
                    });
                }
            }
        ).listen(
            'ToDos\\ToDoDeleted',
            (pusherEvent) => {
                const index = this.data.findIndex(item => item.id === pusherEvent.toDo.id);
                if (index > -1 && this.data[index]) {
                    this.data.splice(index, 1);
                }
            }
        );
    }


    destroy(){
        echo.leave(`${Pluralize(this.todoableType)}.${this.todoableId}.to-dos`);
    }

    highlightToDo(toDoID){
        highlight(document.querySelector("[data-to-do-id='" + toDoID + "']"));
    }

}

import StateMachine from './StateMachine';

class PageState extends StateMachine
{
    initial() {
        return [ PageState.LOADING ];
    }
}

PageState.LOADING = 'loading';
PageState.IDLE = 'idle';
PageState.ERROR = 'error';
PageState.SUBMITTING = 'submitting';

export default PageState;

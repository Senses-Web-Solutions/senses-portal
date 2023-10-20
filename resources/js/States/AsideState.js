import StateMachine from './StateMachine';

class AsideState extends StateMachine 
{
    initial() {
        return [ AsideState.IDLE ];
    }
}

AsideState.LOADING = 'loading';
AsideState.IDLE = 'idle';
AsideState.ERROR = 'error';
AsideState.SUBMITTING = 'submitting';

export default AsideState;
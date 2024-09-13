<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-dark">
            {{ __('Eliminar cuenta') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar su cuenta, descargue cualquier dato o información que desee conservar.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Eliminar cuenta') }}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('¿Está seguro de que desea eliminar su cuenta?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        {{ __('Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. Por favor, ingrese su contraseña para confirmar que desea eliminar su cuenta de forma permanente.') }}
                    </p>

                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Contraseña') }}">
                            @if ($errors->userDeletion->has('password'))
                                <div class="text-danger">
                                    {{ $errors->userDeletion->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Eliminar cuenta') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

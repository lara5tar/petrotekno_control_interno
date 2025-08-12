                                        <select name="vehiculo_id" id="vehiculo_id" 
                                                class="form-control select2" required>
                                            <option value="">Seleccionar vehículo</option>
                                            @foreach($vehiculos as $vehiculo)
                                                <option value="{{ $vehiculo['id'] }}" 
                                                    {{ !$vehiculo['disponible'] ? 'disabled' : '' }}
                                                    data-disponible="{{ $vehiculo['disponible'] ? 'true' : 'false' }}">
                                                    {{ $vehiculo['nombre_completo'] }}
                                                </option>
                                            @endforeach
                                        </select>
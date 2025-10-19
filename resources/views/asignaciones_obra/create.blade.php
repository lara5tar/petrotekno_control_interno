<select name="vehiculo_id" id="vehiculo_id" 
                                                class="form-control select2" required>
                                            <option value="">Seleccionar activo</option>
                                            @foreach($vehiculos as $activo)
                                                <option value="{{ $activo['id'] }}" 
                                                    {{ !$activo['disponible'] ? 'disabled' : '' }}
                                                    data-disponible="{{ $activo['disponible'] ? 'true' : 'false' }}">
                                                    {{ $activo['nombre_completo'] }}
                                                </option>
                                            @endforeach
                                        </select>